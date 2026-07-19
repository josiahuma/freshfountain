<?php

namespace App\Filament\Resources\AttendanceServiceTypes\Pages;

use App\Filament\Resources\AttendanceServiceTypes\AttendanceServiceTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceServiceType extends EditRecord
{
    protected static string $resource = AttendanceServiceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
