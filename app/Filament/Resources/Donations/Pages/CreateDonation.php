<?php

namespace App\Filament\Resources\Donations\Pages;

use App\Filament\Resources\Donations\DonationResource;
use App\Services\Finance\DonationFinanceService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDonation extends CreateRecord
{
    protected static string $resource = DonationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by_user_id'] ??= auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        app(DonationFinanceService::class)
            ->sync($this->getRecord());

        Notification::make()
            ->success()
            ->title('Donation recorded successfully.')
            ->body('The donation has been posted to the finance ledger.')
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', [
            'record' => $this->getRecord(),
        ]);
    }
}