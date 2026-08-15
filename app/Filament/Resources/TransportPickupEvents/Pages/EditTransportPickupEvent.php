<?php

namespace App\Filament\Resources\TransportPickupEvents\Pages;

use App\Filament\Resources\TransportPickupEvents\TransportPickupEventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTransportPickupEvent extends EditRecord
{
    protected static string $resource = TransportPickupEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
