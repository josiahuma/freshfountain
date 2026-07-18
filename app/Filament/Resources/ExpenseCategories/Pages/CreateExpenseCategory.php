<?php

namespace App\Filament\Resources\ExpenseCategories\Pages;

use App\Filament\Resources\ExpenseCategories\ExpenseCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenseCategory extends CreateRecord
{
    protected static string $resource = ExpenseCategoryResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Expense category created successfully.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl();
    }
}