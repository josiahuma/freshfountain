<?php

namespace App\Filament\Resources\FinanceTransactions\Pages;

use App\Filament\Resources\FinanceTransactions\FinanceTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFinanceTransaction extends CreateRecord
{
    protected static string $resource = FinanceTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();
        $data['source'] = $data['source'] ?? 'manual';
        $data['status'] = $data['status'] ?? 'completed';
        $data['currency'] = $data['currency'] ?? 'GBP';

        if (($data['type'] ?? null) === 'expense') {
            $data['income_category_id'] = null;
        }

        if (in_array($data['type'] ?? null, ['income', 'gift_aid'], true)) {
            $data['expense_category_id'] = null;
        }

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Finance transaction recorded successfully.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl();
    }
}