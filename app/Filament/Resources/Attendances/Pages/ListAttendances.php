<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Pages\AttendanceAnalytics;
use App\Filament\Resources\Attendances\AttendanceResource;
use App\Models\Attendance;
use App\Support\Access\BackendPermissions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('analytics')->label('View analytics')->icon('heroicon-o-presentation-chart-line')->url(AttendanceAnalytics::getUrl())
                ->visible(fn (): bool => AttendanceAnalytics::canAccess()),
            Action::make('usherEntry')->label('Usher entry form')->icon('heroicon-o-device-phone-mobile')->url(route('attendance.entry.create'))
                ->visible(fn (): bool => auth()->user()?->can(BackendPermissions::ATTENDANCE_ENTRY) === true || auth()->user()?->isSuperAdmin() === true),
            CreateAction::make()->label('Record attendance'),
        ];
    }

    public function getSubheading(): ?string
    {
        $latest = Attendance::query()->latest('service_date')->first();
        if (! $latest) return 'Record service attendance as numbers only. No individual member check-in is used.';
        return sprintf('Latest: %s on %s — %s people.', $latest->service_name, $latest->service_date->format('d M Y'), number_format($latest->total));
    }
}
