<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationFund;
use App\Services\Finance\DonationFinanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use UnexpectedValueException;

class GivingController extends Controller
{
    public function index(): View
    {
        $funds = DonationFund::query()
            ->active()
            ->ordered()
            ->get();

        return view('giving.index', compact('funds'));
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'donation_fund_id' => [
                'required',
                Rule::exists('donation_funds', 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
            ],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999.99'],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['required', 'email:rfc', 'max:255'],
            'donor_phone' => ['nullable', 'string', 'max:50'],
            'is_anonymous' => ['nullable', 'boolean'],
            'gift_aid' => ['nullable', 'boolean'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'county' => ['nullable', 'string', 'max:120'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:120'],
        ]);

        $fund = DonationFund::query()
            ->active()
            ->findOrFail($validated['donation_fund_id']);

        $isAnonymous = (bool) ($validated['is_anonymous'] ?? false);
        $giftAid = (bool) ($validated['gift_aid'] ?? false);

        $donation = Donation::query()->create([
            'donation_fund_id' => $fund->getKey(),
            'member_id' => auth()->user()?->member?->getKey(),
            'amount' => number_format((float) $validated['amount'], 2, '.', ''),
            'currency' => 'GBP',
            'payment_method' => 'online_payment',
            'is_recurring' => false,
            'recurring_interval' => null,
            'gift_aid' => $giftAid,
            'is_anonymous' => $isAnonymous,
            'recorded_by_user_id' => null,
            'donor_name' => $isAnonymous ? null : ($validated['donor_name'] ?? null),
            'donor_email' => $validated['donor_email'],
            'donor_phone' => $validated['donor_phone'] ?? null,
            'address_line_1' => $giftAid ? ($validated['address_line_1'] ?? null) : null,
            'address_line_2' => $giftAid ? ($validated['address_line_2'] ?? null) : null,
            'city' => $giftAid ? ($validated['city'] ?? null) : null,
            'county' => $giftAid ? ($validated['county'] ?? null) : null,
            'postcode' => $giftAid ? ($validated['postcode'] ?? null) : null,
            'country' => $giftAid ? ($validated['country'] ?? 'United Kingdom') : null,
            'status' => Donation::STATUS_PENDING,
            'payment_provider' => 'stripe',
        ]);

        try {
            $stripe = new StripeClient(config('services.stripe.secret'));

            $session = $stripe->checkout->sessions->create([
                'mode' => 'payment',
                'customer_email' => $donation->donor_email,
                'client_reference_id' => (string) $donation->getKey(),
                'line_items' => [[
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => strtolower($donation->currency),
                        'unit_amount' => (int) round(((float) $donation->amount) * 100),
                        'product_data' => [
                            'name' => $fund->name,
                            'description' => 'Donation to Fresh Fountain Christian Network',
                        ],
                    ],
                ]],
                'metadata' => [
                    'donation_id' => (string) $donation->getKey(),
                    'donation_fund_id' => (string) $fund->getKey(),
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'donation_id' => (string) $donation->getKey(),
                    ],
                ],
                'success_url' => route('giving.success')
                    . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('giving.cancel', [
                    'donation' => $donation->getKey(),
                ]),
            ]);

            $donation->update([
                'stripe_session_id' => $session->id,
            ]);

            return redirect()->away($session->url);
        } catch (Throwable $exception) {
            Log::error('Stripe Checkout session creation failed.', [
                'donation_id' => $donation->getKey(),
                'message' => $exception->getMessage(),
            ]);

            $donation->update([
                'status' => Donation::STATUS_FAILED,
                'failed_at' => now(),
                'failure_reason' => 'Unable to start Stripe Checkout.',
            ]);

            return back()
                ->withInput()
                ->with('giving_error', 'We could not start the secure payment. Please try again.');
        }
    }

    public function success(Request $request): View
    {
        $sessionId = $request->string('session_id')->toString();
        $donation = null;

        if ($sessionId !== '') {
            $donation = Donation::query()
                ->where('stripe_session_id', $sessionId)
                ->first();
        }

        return view('giving.success', compact('donation'));
    }

    public function cancel(Donation $donation): View
    {
        if ($donation->status === Donation::STATUS_PENDING) {
            $donation->update([
                'status' => Donation::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);
        }

        return view('giving.cancel', compact('donation'));
    }

    public function webhook(
        Request $request,
        DonationFinanceService $financeService
    ): Response {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                (string) $signature,
                (string) $secret
            );
        } catch (UnexpectedValueException|SignatureVerificationException $exception) {
            Log::warning('Invalid Stripe webhook received.', [
                'message' => $exception->getMessage(),
            ]);

            return response('Invalid webhook.', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            /** @var Session $session */
            $session = $event->data->object;

            $donationId = $session->metadata->donation_id
                ?? $session->client_reference_id
                ?? null;

            $donation = Donation::query()->find($donationId);

            if ($donation !== null) {
                $donation->update([
                    'status' => Donation::STATUS_PAID,
                    'paid_at' => now(),
                    'failed_at' => null,
                    'cancelled_at' => null,
                    'failure_reason' => null,
                    'stripe_session_id' => $session->id,
                    'stripe_payment_intent_id' => is_string($session->payment_intent)
                        ? $session->payment_intent
                        : $session->payment_intent?->id,
                    'stripe_customer_id' => is_string($session->customer)
                        ? $session->customer
                        : $session->customer?->id,
                    'provider_metadata' => [
                        'stripe_event_id' => $event->id,
                        'payment_status' => $session->payment_status,
                    ],
                ]);

                $financeService->sync($donation->fresh());
            }
        }

        if ($event->type === 'checkout.session.expired') {
            /** @var Session $session */
            $session = $event->data->object;

            Donation::query()
                ->where('stripe_session_id', $session->id)
                ->where('status', Donation::STATUS_PENDING)
                ->update([
                    'status' => Donation::STATUS_CANCELLED,
                    'cancelled_at' => now(),
                ]);
        }

        return response('Webhook handled.', 200);
    }
}