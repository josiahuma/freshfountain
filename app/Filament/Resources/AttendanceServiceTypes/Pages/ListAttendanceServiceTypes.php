<?php

namespace App\Filament\Resources\AttendanceServiceTypes\Pages;

use App\Filament\Resources\AttendanceServiceTypes\AttendanceServiceTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceServiceTypes extends ListRecords
{
    protected static string $resource = AttendanceServiceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
