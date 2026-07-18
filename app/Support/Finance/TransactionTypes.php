<?php

namespace App\Support\Finance;

class TransactionTypes
{
    public const INCOME = 'income';

    public const EXPENSE = 'expense';

    public const GIFT_AID = 'gift_aid';

    public const ADJUSTMENT = 'adjustment';

    public static function options(): array
    {
        return [
            self::INCOME => 'Income',
            self::EXPENSE => 'Expense',
            self::GIFT_AID => 'Gift Aid',
            self::ADJUSTMENT => 'Adjustment',
        ];
    }

    public static function label(?string $type): string
    {
        if (blank($type)) {
            return 'Unknown';
        }

        return self::options()[$type]
            ?? str($type)->replace('_', ' ')->title()->toString();
    }

    public static function color(?string $type): string
    {
        return match ($type) {
            self::INCOME => 'success',
            self::EXPENSE => 'danger',
            self::GIFT_AID => 'warning',
            self::ADJUSTMENT => 'gray',
            default => 'gray',
        };
    }

    public static function usesIncomeCategory(?string $type): bool
    {
        return in_array($type, [
            self::INCOME,
            self::GIFT_AID,
            self::ADJUSTMENT,
        ], true);
    }

    public static function requiresIncomeCategory(?string $type): bool
    {
        return in_array($type, [
            self::INCOME,
            self::GIFT_AID,
        ], true);
    }

    public static function usesExpenseCategory(?string $type): bool
    {
        return in_array($type, [
            self::EXPENSE,
            self::ADJUSTMENT,
        ], true);
    }

    public static function requiresExpenseCategory(?string $type): bool
    {
        return $type === self::EXPENSE;
    }
}