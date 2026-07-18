<?php

namespace App\Support\Privacy;

use App\Models\FinanceTransaction;
use App\Support\Access\BackendAccess;

final class DonorPrivacy
{
    public static function canViewIdentity(): bool
    {
        return BackendAccess::canViewDonorIdentities();
    }

    public static function name(?string $value): string
    {
        if (self::canViewIdentity()) {
            return filled($value) ? $value : 'Guest Donor';
        }

        return filled($value) ? self::maskWords($value) : 'Hidden Donor';
    }

    public static function email(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (self::canViewIdentity()) {
            return $value;
        }

        [$local, $domain] = array_pad(explode('@', $value, 2), 2, '');

        return mb_substr($local, 0, 1)
            .str_repeat('•', max(3, mb_strlen($local) - 1))
            .($domain !== '' ? '@'.$domain : '');
    }

    public static function phone(?string $value): ?string
    {
        if (blank($value) || self::canViewIdentity()) {
            return $value;
        }

        $last = mb_substr($value, -3);

        return str_repeat('•', max(5, mb_strlen($value) - 3)).$last;
    }

    public static function text(?string $value): ?string
    {
        if (blank($value) || self::canViewIdentity()) {
            return $value;
        }

        return 'Hidden';
    }

    public static function transactionDescription(
        ?string $value,
        FinanceTransaction|bool|null $transaction = null,
    ): ?string {
        if (blank($value) || self::canViewIdentity()) {
            return $value;
        }

        $isDonorLinked = $transaction instanceof FinanceTransaction
            ? $transaction->isDonorLinked()
            : (bool) $transaction;

        if (! $isDonorLinked) {
            return $value;
        }

        if (preg_match('/^(.*?\bfrom\s+)(.+)$/iu', $value, $matches) === 1) {
            return $matches[1].self::maskWords($matches[2]);
        }

        if (preg_match('/^(.*?[-–—:]\s*)([^-–—:]+)$/u', $value, $matches) === 1) {
            return $matches[1].self::maskWords($matches[2]);
        }

        return 'Donation from Hidden Donor';
    }

    private static function maskWords(string $value): string
    {
        return collect(preg_split('/\s+/', trim($value)) ?: [])
            ->filter()
            ->map(fn (string $word): string => self::maskWord($word))
            ->implode(' ');
    }

    private static function maskWord(string $word): string
    {
        $leading = '';
        $trailing = '';

        if (preg_match('/^([^\pL\pN]*)(.*?)([^\pL\pN]*)$/u', $word, $parts) === 1) {
            $leading = $parts[1];
            $word = $parts[2];
            $trailing = $parts[3];
        }

        if ($word === '') {
            return $leading.$trailing;
        }

        return $leading
            .mb_substr($word, 0, 1)
            .str_repeat('•', max(3, mb_strlen($word) - 1))
            .$trailing;
    }
}
