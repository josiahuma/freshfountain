<?php

namespace App\Filament\Resources\Donations\Pages;

use App\Filament\Resources\Donations\DonationResource;
use App\Services\Finance\DonationFinanceService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDonation extends EditRecord
{
    protected static string $resource = DonationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['recorded_by_user_id'] ??= auth()->id();

        return $data;
    }

    protected function afterSave(): void
    {
        app(DonationFinanceService::class)
            ->sync($this->getRecord());

        Notification::make()
            ->success()
            ->title('Donation updated successfully.')
            ->body('The linked finance ledger entry has also been updated.')
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', [
            'record' => $this->getRecord(),
        ]);
    }
}