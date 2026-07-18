<?php

namespace App\Support\Finance;

use Filament\Tables\Columns\TextColumn;

class Money
{
    public const DEFAULT_CURRENCY = 'GBP';

    public static function column(
        string $name = 'amount',
        string $label = 'Amount'
    ): TextColumn {
        return TextColumn::make($name)
            ->label($label)
            ->money(self::DEFAULT_CURRENCY)
            ->alignEnd()
            ->sortable();
    }

    public static function format(
        int|float|string|null $amount,
        string $currency = self::DEFAULT_CURRENCY
    ): string {
        $numericAmount = is_numeric($amount)
            ? (float) $amount
            : 0.0;

        return match ($currency) {
            'GBP' => '£' . number_format($numericAmount, 2),
            'EUR' => '€' . number_format($numericAmount, 2),
            'USD' => '$' . number_format($numericAmount, 2),
            default => $currency . ' ' . number_format($numericAmount, 2),
        };
    }

    public static function currencies(): array
    {
        return [
            'GBP' => 'GBP — British Pound',
        ];
    }
}