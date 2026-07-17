<?php

namespace App\Filament\Resources\ChurchUnits\Pages;

use App\Filament\Resources\ChurchUnits\ChurchUnitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChurchUnits extends ListRecords
{
    protected static string $resource = ChurchUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
