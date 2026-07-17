<?php

namespace App\Filament\Resources\ChurchUnits\Pages;

use App\Filament\Resources\ChurchUnits\ChurchUnitResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewChurchUnit extends ViewRecord
{
    protected static string $resource = ChurchUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
