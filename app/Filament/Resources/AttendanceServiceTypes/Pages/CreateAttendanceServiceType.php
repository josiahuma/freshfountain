<?php

namespace App\Filament\Resources\AttendanceServiceTypes\Pages;

use App\Filament\Resources\AttendanceServiceTypes\AttendanceServiceTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendanceServiceType extends CreateRecord
{
    protected static string $resource = AttendanceServiceTypeResource::class;
}
