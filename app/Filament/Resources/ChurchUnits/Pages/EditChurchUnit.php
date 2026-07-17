<?php

namespace App\Filament\Resources\ChurchUnits\Pages;

use App\Filament\Resources\ChurchUnits\ChurchUnitResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditChurchUnit extends EditRecord
{
    protected static string $resource = ChurchUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
