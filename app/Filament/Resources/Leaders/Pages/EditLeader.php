<?php

namespace App\Filament\Resources\Leaders\Pages;

use App\Filament\Resources\Leaders\LeaderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLeader extends EditRecord
{
    protected static string $resource = LeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
