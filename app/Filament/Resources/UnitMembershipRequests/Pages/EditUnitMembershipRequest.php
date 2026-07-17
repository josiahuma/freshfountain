<?php

namespace App\Filament\Resources\UnitMembershipRequests\Pages;

use App\Filament\Resources\UnitMembershipRequests\UnitMembershipRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUnitMembershipRequest extends EditRecord
{
    protected static string $resource = UnitMembershipRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
