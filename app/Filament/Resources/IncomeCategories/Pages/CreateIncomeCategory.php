<?php

namespace App\Filament\Resources\IncomeCategories\Pages;

use App\Filament\Resources\IncomeCategories\IncomeCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIncomeCategory extends CreateRecord
{
    protected static string $resource = IncomeCategoryResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Income category created successfully.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl();
    }
}