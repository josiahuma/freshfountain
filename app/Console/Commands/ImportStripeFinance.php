<?php

namespace App\Console\Commands;

use App\Models\Donation;
use App\Models\DonationFund;
use App\Models\FinanceTransaction;
use App\Services\Finance\DonationFinanceService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stripe\Charge;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Throwable;

class ImportStripeFinance extends Command
{
    protected $signature = 'stripe:import-finance
        {--dry-run : Analyse Stripe records without writing anything}
        {--update-existing : Refresh donations already matched by Payment Intent ID}
        {--default-fund=Offering : Donation fund used when Stripe metadata has no fund}
        {--from= : Earliest Stripe payment date in YYYY-MM-DD format}
        {--to= : Latest Stripe payment date in YYYY-MM-DD format}
        {--force : Skip confirmation when using a live Stripe secret key}';

    protected $description =
        'Backfill successful Stripe payments into donations and finance transactions.';

    private bool $dryRun = false;

    private bool $updateExisting = false;

    private array $stats = [
        'payment_intents_scanned' => 0,
        'successful_payments_found' => 0,
        'matched_existing_donations' => 0,
        'donations_created' => 0,
        'donations_updated' => 0,
        'fully_refunded' => 0,
        'partially_refunded' => 0,
        'failed_or_cancelled_skipped' => 0,
        'zero_value_skipped' => 0,
        'finance_transactions_synced' => 0,
        'records_failed' => 0,
    ];

    public function handle(
        DonationFinanceService $financeService
    ): int {
        $this->dryRun =
            (bool) $this->option('dry-run');

        $this->updateExisting =
            (bool) $this->option(
                'update-existing'
            );

        $secret = (string) config(
            'services.stripe.secret'
        );

        if ($secret === '') {
            $this->error(
                'STRIPE_SECRET is missing from the environment.'
            );

            return self::FAILURE;
        }

        if (
            str_starts_with($secret, 'sk_live_')
            && ! $this->option('force')
            && ! $this->confirm(
                'This will read LIVE Stripe payment history. Continue?'
            )
        ) {
            $this->warn('Import cancelled.');

            return self::SUCCESS;
        }

        $defaultFund =
            $this->resolveDefaultFund();

        if ($defaultFund === null) {
            $this->error(
                'No active donation fund could be resolved.'
            );

            return self::FAILURE;
        }

        $params = [
            'limit' => 100,
            'expand' => [
                'data.latest_charge',
            ],
        ];

        if ($created = $this->createdFilter()) {
            $params['created'] = $created;
        }

        $stripe = new StripeClient($secret);

        $this->newLine();

        $this->info(
            $this->dryRun
                ? 'Stripe finance backfill — DRY RUN'
                : 'Importing Stripe payment history'
        );

        try {
            $paymentIntents =
                $stripe->paymentIntents->all(
                    $params
                );

            foreach (
                $paymentIntents
                    ->autoPagingIterator()
                as $paymentIntent
            ) {
                $this->stats[
                    'payment_intents_scanned'
                ]++;

                $this->processPaymentIntent(
                    $stripe,
                    $paymentIntent,
                    $defaultFund,
                    $financeService
                );
            }

            $this->displaySummary();

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);

            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }
    }

    private function processPaymentIntent(
        StripeClient $stripe,
        PaymentIntent $paymentIntent,
        DonationFund $defaultFund,
        DonationFinanceService $financeService
    ): void {
        if ($paymentIntent->status !== 'succeeded') {
            $this->stats[
                'failed_or_cancelled_skipped'
            ]++;

            return;
        }

        $this->stats[
            'successful_payments_found'
        ]++;

        $charge = $this->resolveCharge(
            $stripe,
            $paymentIntent
        );

        $grossMinor = (int) (
            $paymentIntent->amount_received
            ?: $paymentIntent->amount
            ?: 0
        );

        $refundedMinor = (int) (
            $charge?->amount_refunded
            ?? 0
        );

        if ($grossMinor <= 0) {
            $this->stats[
                'zero_value_skipped'
            ]++;

            return;
        }

        $fullyRefunded =
            $refundedMinor >= $grossMinor;

        $partiallyRefunded =
            $refundedMinor > 0
            && ! $fullyRefunded;

        if ($fullyRefunded) {
            $this->stats['fully_refunded']++;
        }

        if ($partiallyRefunded) {
            $this->stats[
                'partially_refunded'
            ]++;
        }

        $existing = Donation::query()
            ->where(
                'stripe_payment_intent_id',
                $paymentIntent->id
            )
            ->first();

        if ($existing) {
            $this->stats[
                'matched_existing_donations'
            ]++;
        }

        if (
            $existing
            && ! $this->updateExisting
        ) {
            return;
        }

        $fund = $this->resolveFund(
            $paymentIntent,
            $defaultFund
        );

        $billing =
            $charge?->billing_details;

        $currency = strtoupper(
            (string) $paymentIntent->currency
        );

        $grossAmount =
            $grossMinor / 100;

        $refundedAmount =
            $refundedMinor / 100;

        $netAmount =
            max(
                0,
                $grossAmount
                - $refundedAmount
            );

        $status = $fullyRefunded
            ? 'refunded'
            : Donation::STATUS_PAID;

        $notes = collect([
            'Imported directly from Stripe.',
            $fullyRefunded
                ? 'This payment was fully refunded.'
                : null,
            $partiallyRefunded
                ? sprintf(
                    'Partially refunded: %s %.2f. Net retained: %s %.2f.',
                    $currency,
                    $refundedAmount,
                    $currency,
                    $netAmount
                )
                : null,
            'Stripe Payment Intent: '
                . $paymentIntent->id,
            $charge?->id
                ? 'Stripe Charge: '
                    . $charge->id
                : null,
        ])
            ->filter()
            ->implode(PHP_EOL);

        $payload = [
            'donation_fund_id' =>
                $fund->getKey(),
            'member_id' => null,
            'amount' =>
                $partiallyRefunded
                    ? $netAmount
                    : $grossAmount,
            'currency' => $currency,
            'payment_method' =>
                'online_payment',
            'is_recurring' =>
                false,
            'recurring_interval' => null,
            'gift_aid' => false,
            'is_anonymous' =>
                blank($billing?->name)
                && blank($billing?->email)
                && blank(
                    $paymentIntent
                        ->receipt_email
                ),
            'recorded_by_user_id' => null,
            'donor_name' =>
                $this->cleanText(
                    $billing?->name
                ),
            'donor_email' =>
                $this->cleanText(
                    $billing?->email
                    ?: $paymentIntent
                        ->receipt_email
                ),
            'donor_phone' =>
                $this->cleanText(
                    $billing?->phone
                ),
            'address_line_1' =>
                $this->cleanText(
                    $billing?->address?->line1
                ),
            'address_line_2' =>
                $this->cleanText(
                    $billing?->address?->line2
                ),
            'city' =>
                $this->cleanText(
                    $billing?->address?->city
                ),
            'county' =>
                $this->cleanText(
                    $billing?->address?->state
                ),
            'postcode' =>
                $this->cleanText(
                    $billing?->address
                        ?->postal_code
                ),
            'country' =>
                $this->cleanText(
                    $billing?->address?->country
                ),
            'status' => $status,
            'payment_provider' => 'stripe',
            'stripe_session_id' =>
                $this->metadataValue(
                    $paymentIntent,
                    'checkout_session_id'
                ),
            'stripe_payment_intent_id' =>
                $paymentIntent->id,
            'stripe_subscription_id' =>
                $this->metadataValue(
                    $paymentIntent,
                    'subscription_id'
                ),
            'stripe_customer_id' =>
                is_string(
                    $paymentIntent->customer
                )
                    ? $paymentIntent->customer
                    : $paymentIntent
                        ->customer?->id,
            'paid_at' =>
                CarbonImmutable::createFromTimestamp(
                    (int) $paymentIntent->created
                ),
            'failed_at' => null,
            'cancelled_at' => null,
            'failure_reason' => null,
            'notes' => $notes,
            'provider_metadata' => [
                'stripe_charge_id' =>
                    $charge?->id,
                'stripe_status' =>
                    $paymentIntent->status,
                'gross_amount_minor' =>
                    $grossMinor,
                'refunded_amount_minor' =>
                    $refundedMinor,
                'payment_method_type' =>
                    $charge
                        ?->payment_method_details
                        ?->type,
                'stripe_metadata' =>
                    $paymentIntent
                        ->metadata
                        ?->toArray(),
            ],
            'legacy_ovibase_id' =>
                $existing
                    ?->legacy_ovibase_id,
            'legacy_tenant_id' =>
                $existing
                    ?->legacy_tenant_id,
        ];

        if ($this->dryRun) {
            if ($existing) {
                $this->stats[
                    'donations_updated'
                ]++;
            } else {
                $this->stats[
                    'donations_created'
                ]++;
            }

            return;
        }

        DB::transaction(
            function () use (
                $existing,
                $payload,
                $paymentIntent,
                $financeService,
                $fullyRefunded,
                $grossAmount,
                $currency,
                $notes
            ): void {
                if ($existing) {
                    $existing->update($payload);

                    $donation =
                        $existing->fresh();

                    $this->stats[
                        'donations_updated'
                    ]++;
                } else {
                    $donation =
                        Donation::query()
                            ->create($payload);

                    $this->stats[
                        'donations_created'
                    ]++;
                }

                DB::table('donations')
                    ->where(
                        'id',
                        $donation->getKey()
                    )
                    ->update([
                        'created_at' =>
                            CarbonImmutable
                                ::createFromTimestamp(
                                    (int)
                                    $paymentIntent
                                        ->created
                                )
                                ->format(
                                    'Y-m-d H:i:s'
                                ),
                    ]);

                if ($fullyRefunded) {
                    $this->syncRefundedTransaction(
                        $donation,
                        $grossAmount,
                        $currency,
                        $notes
                    );
                } else {
                    $financeService->sync(
                        $donation->fresh()
                    );
                }

                $this->stats[
                    'finance_transactions_synced'
                ]++;
            }
        );
    }

    private function syncRefundedTransaction(
        Donation $donation,
        float $grossAmount,
        string $currency,
        string $notes
    ): void {
        $donation->loadMissing(
            'donationFund'
        );

        FinanceTransaction::query()
            ->updateOrCreate(
                [
                    'donation_id' =>
                        $donation->getKey(),
                ],
                [
                    'type' =>
                        FinanceTransaction
                            ::TYPE_INCOME,
                    'income_category_id' =>
                        $donation
                            ->donationFund
                            ?->income_category_id,
                    'expense_category_id' =>
                        null,
                    'created_by_user_id' =>
                        null,
                    'amount' =>
                        $grossAmount,
                    'currency' =>
                        $currency,
                    'transaction_date' =>
                        $donation
                            ->paid_at
                            ?->toDateString()
                        ?? now()->toDateString(),
                    'description' =>
                        ($donation
                            ->donationFund
                            ?->name
                            ?? 'Donation')
                        . ' from '
                        . $donation
                            ->donor_display_name,
                    'notes' => $notes,
                    'reference' =>
                        'DON-'
                        . str_pad(
                            (string)
                            $donation
                                ->getKey(),
                            8,
                            '0',
                            STR_PAD_LEFT
                        ),
                    'payment_method' =>
                        'online_payment',
                    'source' =>
                        FinanceTransaction
                            ::SOURCE_DONATION,
                    'status' =>
                        FinanceTransaction
                            ::STATUS_VOID,
                    'legacy_category_name' =>
                        $donation
                            ->donationFund
                            ?->name,
                    'legacy_ovibase_id' =>
                        null,
                    'legacy_tenant_id' =>
                        $donation
                            ->legacy_tenant_id,
                ]
            );
    }

    private function resolveCharge(
        StripeClient $stripe,
        PaymentIntent $paymentIntent
    ): ?Charge {
        $latestCharge =
            $paymentIntent->latest_charge;

        if ($latestCharge instanceof Charge) {
            return $latestCharge;
        }

        if (is_string($latestCharge)) {
            return $stripe
                ->charges
                ->retrieve(
                    $latestCharge,
                    []
                );
        }

        return null;
    }

    private function resolveFund(
        PaymentIntent $paymentIntent,
        DonationFund $defaultFund
    ): DonationFund {
        $fundId = $this->metadataValue(
            $paymentIntent,
            'donation_fund_id'
        );

        if (filled($fundId)) {
            $fund = DonationFund::query()
                ->active()
                ->find($fundId);

            if ($fund) {
                return $fund;
            }
        }

        $fundName =
            $this->metadataValue(
                $paymentIntent,
                'donation_fund'
            )
            ?: $this->metadataValue(
                $paymentIntent,
                'fund'
            );

        if (filled($fundName)) {
            $fund = DonationFund::query()
                ->active()
                ->whereRaw(
                    'LOWER(name) = ?',
                    [
                        Str::lower(
                            trim($fundName)
                        ),
                    ]
                )
                ->first();

            if ($fund) {
                return $fund;
            }
        }

        return $defaultFund;
    }

    private function resolveDefaultFund(): ?DonationFund
    {
        $name = trim(
            (string) $this->option(
                'default-fund'
            )
        );

        return DonationFund::query()
            ->active()
            ->whereRaw(
                'LOWER(name) = ?',
                [Str::lower($name)]
            )
            ->first()
            ?? DonationFund::query()
                ->active()
                ->where(
                    'is_default',
                    true
                )
                ->first()
            ?? DonationFund::query()
                ->active()
                ->ordered()
                ->first();
    }

    private function createdFilter(): array
    {
        $filter = [];

        if (
            filled($this->option('from'))
        ) {
            $filter['gte'] =
                CarbonImmutable::parse(
                    (string)
                    $this->option('from')
                )
                    ->startOfDay()
                    ->timestamp;
        }

        if (
            filled($this->option('to'))
        ) {
            $filter['lte'] =
                CarbonImmutable::parse(
                    (string)
                    $this->option('to')
                )
                    ->endOfDay()
                    ->timestamp;
        }

        return $filter;
    }

    private function metadataValue(
        PaymentIntent $paymentIntent,
        string $key
    ): ?string {
        $value =
            $paymentIntent
                ->metadata[$key]
            ?? null;

        return filled($value)
            ? (string) $value
            : null;
    }

    private function cleanText(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value === ''
            ? null
            : $value;
    }

    private function displaySummary(): void
    {
        $this->newLine();

        $this->table(
            ['Result', 'Count'],
            collect($this->stats)
                ->map(
                    fn (
                        int $count,
                        string $label
                    ): array => [
                        Str::headline($label),
                        number_format($count),
                    ]
                )
                ->values()
                ->all()
        );

        $this->newLine();

        if ($this->dryRun) {
            $this->warn(
                'Dry run complete. No records were written.'
            );
        } else {
            $this->info(
                'Stripe finance backfill completed.'
            );

            $this->line(
                'The command is safe to rerun because Payment Intent IDs prevent duplicates.'
            );
        }
    }
}
