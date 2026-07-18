<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Donation extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    public const INTERVAL_WEEKLY = 'weekly';

    public const INTERVAL_MONTHLY = 'monthly';

    public const INTERVAL_YEARLY = 'yearly';

    protected $fillable = [
        'donation_fund_id',
        'member_id',
        'amount',
        'currency',
        'payment_method',
        'is_recurring',
        'recurring_interval',
        'gift_aid',
        'is_anonymous',
        'recorded_by_user_id',
        'donor_name',
        'donor_email',
        'donor_phone',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
        'country',
        'status',
        'payment_provider',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'paid_at',
        'failed_at',
        'cancelled_at',
        'failure_reason',
        'notes',
        'provider_metadata',
        'legacy_ovibase_id',
        'legacy_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_recurring' => 'boolean',
            'gift_aid' => 'boolean',
            'is_anonymous' => 'boolean',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'provider_metadata' => 'array',
        ];
    }

    public function donationFund(): BelongsTo
    {
        return $this->belongsTo(
            DonationFund::class
        );
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(
            Member::class
        );
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by_user_id'
        );
    }

    public function financeTransaction(): HasOne
    {
        return $this->hasOne(
            FinanceTransaction::class
        );
    }

    public function scopePaid(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            self::STATUS_PAID
        );
    }

    public function scopePending(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            self::STATUS_PENDING
        );
    }

    public function scopeGiftAid(
        Builder $query
    ): Builder {
        return $query->where(
            'gift_aid',
            true
        );
    }

    public function getFormattedAmountAttribute(): string
    {
        return sprintf(
            '%s%.2f',
            $this->currencySymbol(),
            (float) $this->amount
        );
    }

    public function getDonorDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous donor';
        }

        return $this->donor_name
            ?: $this->member?->full_name
            ?: $this->donor_email
            ?: 'Guest donor';
    }

    public function currencySymbol(): string
    {
        return match (
            strtoupper($this->currency)
        ) {
            'GBP' => '£',
            'EUR' => '€',
            'USD' => '$',
            default => strtoupper(
                $this->currency
            ) . ' ',
        };
    }
}