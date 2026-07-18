<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class FinanceTransaction extends Model
{
    use HasFactory;

    public const TYPE_INCOME = 'income';

    public const TYPE_EXPENSE = 'expense';

    public const SOURCE_MANUAL = 'manual';

    public const SOURCE_DONATION = 'donation';

    public const SOURCE_GIFT_AID = 'gift_aid';

    public const SOURCE_IMPORT = 'ovibase_import';

    public const SOURCE_ADJUSTMENT = 'adjustment';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_PENDING = 'pending';

    public const STATUS_VOID = 'void';

    protected $fillable = [
        'type',
        'income_category_id',
        'expense_category_id',
        'donation_id',
        'created_by_user_id',
        'amount',
        'currency',
        'transaction_date',
        'description',
        'notes',
        'reference',
        'payment_method',
        'source',
        'status',
        'legacy_category_name',
        'legacy_ovibase_id',
        'legacy_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(
            function (
                FinanceTransaction $transaction
            ): void {
                $transaction->validateCategory();
            }
        );
    }

    public function incomeCategory(): BelongsTo
    {
        return $this->belongsTo(
            IncomeCategory::class
        );
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(
            ExpenseCategory::class
        );
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(
            Donation::class
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by_user_id'
        );
    }

    public function scopeIncome(
        Builder $query
    ): Builder {
        return $query->where(
            'type',
            self::TYPE_INCOME
        );
    }

    public function scopeExpense(
        Builder $query
    ): Builder {
        return $query->where(
            'type',
            self::TYPE_EXPENSE
        );
    }

    public function scopeCompleted(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            self::STATUS_COMPLETED
        );
    }

    public function scopeBetweenDates(
        Builder $query,
        string $from,
        string $to
    ): Builder {
        return $query->whereBetween(
            'transaction_date',
            [
                $from,
                $to,
            ]
        );
    }

    public function getCategoryNameAttribute(): string
    {
        if (
            $this->type === self::TYPE_INCOME
        ) {
            return $this->incomeCategory?->name
                ?? $this->legacy_category_name
                ?? 'Uncategorised income';
        }

        return $this->expenseCategory?->name
            ?? $this->legacy_category_name
            ?? 'Uncategorised expense';
    }

    public function getFormattedAmountAttribute(): string
    {
        return sprintf(
            '%s%.2f',
            $this->currencySymbol(),
            (float) $this->amount
        );
    }

    public function getSignedAmountAttribute(): float
    {
        $amount = (float) $this->amount;

        return $this->type === self::TYPE_EXPENSE
            ? -$amount
            : $amount;
    }


    public function isDonorLinked(): bool
    {
        return filled($this->donation_id)
            || $this->source === self::SOURCE_DONATION;
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

    private function validateCategory(): void
    {
        if (
            $this->type === self::TYPE_INCOME
        ) {
            $this->expense_category_id = null;

            return;
        }

        if (
            $this->type === self::TYPE_EXPENSE
        ) {
            $this->income_category_id = null;

            return;
        }

        throw ValidationException::withMessages([
            'type' => 'Transaction type must be income or expense.',
        ]);
    }
}