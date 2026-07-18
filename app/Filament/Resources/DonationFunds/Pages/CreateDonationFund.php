<?php

namespace App\Filament\Resources\DonationFunds\Pages;

use App\Filament\Resources\DonationFunds\DonationFundResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDonationFund extends CreateRecord
{
    protected static string $resource = DonationFundResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['is_default'])) {
            \App\Models\DonationFund::query()->update([
                'is_default' => false,
            ]);
        }

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Donation fund created successfully.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl();
    }
}