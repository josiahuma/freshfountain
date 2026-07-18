<?php

namespace App\Support\Finance;

class TransactionStatuses
{
    public const PENDING = 'pending';

    public const COMPLETED = 'completed';

    public const CANCELLED = 'cancelled';

    public const REFUNDED = 'refunded';

    public static function options(): array
    {
        return [
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        ];
    }

    public static function label(?string $status): string
    {
        if (blank($status)) {
            return 'Unknown';
        }

        return self::options()[$status]
            ?? str($status)->replace('_', ' ')->title()->toString();
    }

    public static function color(?string $status): string
    {
        return match ($status) {
            self::COMPLETED => 'success',
            self::PENDING => 'warning',
            self::CANCELLED => 'danger',
            self::REFUNDED => 'gray',
            default => 'gray',
        };
    }
}