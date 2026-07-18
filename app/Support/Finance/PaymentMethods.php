<?php

namespace App\Support\Finance;

class PaymentMethods
{
    public const CASH = 'cash';

    public const BANK_TRANSFER = 'bank_transfer';

    public const CARD = 'card';

    public const CHEQUE = 'cheque';

    public const DIRECT_DEBIT = 'direct_debit';

    public const STANDING_ORDER = 'standing_order';

    public const ONLINE = 'online';

    public const OTHER = 'other';

    public static function options(): array
    {
        return [
            self::CASH => 'Cash',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::CARD => 'Card',
            self::CHEQUE => 'Cheque',
            self::DIRECT_DEBIT => 'Direct Debit',
            self::STANDING_ORDER => 'Standing Order',
            self::ONLINE => 'Online Payment',
            self::OTHER => 'Other',
        ];
    }

    public static function label(?string $method): string
    {
        if (blank($method)) {
            return 'Not specified';
        }

        return self::options()[$method]
            ?? str($method)->replace('_', ' ')->title()->toString();
    }

    public static function color(?string $method): string
    {
        return match ($method) {
            self::CASH => 'success',
            self::BANK_TRANSFER => 'info',
            self::CARD => 'warning',
            self::CHEQUE => 'gray',
            self::DIRECT_DEBIT,
            self::STANDING_ORDER => 'primary',
            self::ONLINE => 'info',
            default => 'gray',
        };
    }
}