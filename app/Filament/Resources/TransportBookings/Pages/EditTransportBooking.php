<?php

namespace App\Filament\Resources\TransportBookings\Pages;

use App\Filament\Resources\TransportBookings\TransportBookingResource;
use App\Services\Messaging\TransportNotificationService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTransportBooking extends EditRecord
{
    protected static string $resource = TransportBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resendNotifications')
                ->label('Resend notifications')
                ->icon('heroicon-o-bell-alert')
                ->requiresConfirmation()
                ->action(function (TransportNotificationService $service): void {
                    $results = $service->notifyNewBooking(
                        $this->record->fresh(['pickupEvent']) ?? $this->record
                    );

                    $failed = collect($results)
                        ->flatten(1)
                        ->contains(fn (array $result): bool => ! ($result['ok'] ?? false));

                    if ($failed) {
                        Notification::make()
                            ->title('Notifications attempted')
                            ->body('At least one notification failed. Check the application log / notification logs for details.')
                            ->warning()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Notifications sent')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }
}
