<?php

namespace App\Filament\Resources\Leaders\Pages;

use App\Filament\Resources\Leaders\LeaderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLeader extends ViewRecord
{
    protected static string $resource = LeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
