<?php

namespace App\Services\Finance;

use App\Models\Donation;
use App\Models\FinanceTransaction;
use Illuminate\Support\Facades\DB;

class DonationFinanceService
{
    public function sync(Donation $donation): FinanceTransaction
    {
        return DB::transaction(function () use ($donation): FinanceTransaction {
            $donation->loadMissing('donationFund');

            $transaction = FinanceTransaction::query()
                ->firstOrNew([
                    'donation_id' => $donation->getKey(),
                ]);

            $transaction->fill([
                'type' => FinanceTransaction::TYPE_INCOME,
                'income_category_id' => $donation->donationFund?->income_category_id,
                'expense_category_id' => null,
                'created_by_user_id' => $donation->recorded_by_user_id,
                'amount' => $donation->amount,
                'currency' => strtoupper($donation->currency ?: 'GBP'),
                'transaction_date' => $this->transactionDate($donation),
                'description' => $this->description($donation),
                'notes' => $this->notes($donation),
                'reference' => $this->reference($donation),
                'payment_method' => $donation->payment_method,
                'source' => FinanceTransaction::SOURCE_DONATION,
                'status' => $this->transactionStatus($donation),
                'legacy_category_name' => $donation->donationFund?->name,
                'legacy_ovibase_id' => null,
                'legacy_tenant_id' => $donation->legacy_tenant_id,
            ]);

            $transaction->save();

            return $transaction->fresh([
                'incomeCategory',
                'donation',
                'createdBy',
            ]);
        });
    }

    public function remove(Donation $donation): void
    {
        DB::transaction(function () use ($donation): void {
            FinanceTransaction::query()
                ->where('donation_id', $donation->getKey())
                ->delete();
        });
    }

    private function transactionStatus(Donation $donation): string
    {
        return match ($donation->status) {
            Donation::STATUS_PAID => FinanceTransaction::STATUS_COMPLETED,
            Donation::STATUS_PENDING => FinanceTransaction::STATUS_PENDING,
            Donation::STATUS_FAILED,
            Donation::STATUS_CANCELLED => FinanceTransaction::STATUS_VOID,
            default => FinanceTransaction::STATUS_PENDING,
        };
    }

    private function transactionDate(Donation $donation): string
    {
        return (
            $donation->paid_at
            ?? $donation->created_at
            ?? now()
        )->toDateString();
    }

    private function description(Donation $donation): string
    {
        $fund = $donation->donationFund?->name ?: 'Donation';
        $donor = $donation->donor_display_name;

        return "{$fund} from {$donor}";
    }

    private function reference(Donation $donation): string
    {
        return 'DON-' . str_pad(
            (string) $donation->getKey(),
            8,
            '0',
            STR_PAD_LEFT
        );
    }

    private function notes(Donation $donation): ?string
    {
        $details = collect([
            $donation->notes,
            $donation->stripe_payment_intent_id
                ? 'Stripe payment intent: ' . $donation->stripe_payment_intent_id
                : null,
            $donation->stripe_session_id
                ? 'Stripe checkout session: ' . $donation->stripe_session_id
                : null,
        ])->filter();

        return $details->isEmpty()
            ? null
            : $details->implode(PHP_EOL);
    }
}