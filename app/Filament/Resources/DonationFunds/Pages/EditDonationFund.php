<?php

namespace App\Filament\Resources\DonationFunds\Pages;

use App\Filament\Resources\DonationFunds\DonationFundResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDonationFund extends EditRecord
{
    protected static string $resource = DonationFundResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['is_default'])) {
            \App\Models\DonationFund::whereKeyNot($this->record->getKey())
                ->update([
                    'is_default' => false,
                ]);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Donation fund updated successfully.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl();
    }
}