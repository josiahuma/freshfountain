<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AttendanceAnalyticsOverview;
use App\Filament\Widgets\AttendanceBreakdownChart;
use App\Filament\Widgets\AttendanceServiceComparisonChart;
use App\Filament\Widgets\AttendanceTrendChart;
use App\Models\Attendance;
use App\Models\AttendanceServiceType;
use App\Support\Access\BackendPermissions;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Url;
use UnitEnum;

class AttendanceAnalytics extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;
    protected static string|UnitEnum|null $navigationGroup = 'Church Management';
    protected static ?string $navigationLabel = 'Attendance Analytics';
    protected static ?string $title = 'Attendance Analytics';
    protected static ?string $slug = 'attendance-analytics';
    protected static ?int $navigationSort = 26;
    protected string $view = 'filament.pages.attendance-analytics';

    #[Url]
    public ?int $year = null;

    #[Url]
    public ?int $serviceTypeId = null;

    public function mount(): void
    {
        $this->year ??= (int) (Attendance::query()->selectRaw('MAX(YEAR(service_date)) as max_year')->value('max_year') ?? now()->year);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user?->isSuperAdmin() === true || $user?->can(BackendPermissions::ATTENDANCE_ANALYTICS) === true;
    }

    public static function shouldRegisterNavigation(): bool { return static::canAccess(); }
    public function getColumns(): int|array { return 1; }

    public function getYears(): array
    {
        return Attendance::query()->selectRaw('YEAR(service_date) year')->distinct()->orderByDesc('year')->pluck('year')->mapWithKeys(fn ($year) => [(int) $year => (string) $year])->all();
    }

    public function getServiceTypes(): array
    {
        return AttendanceServiceType::query()->orderBy('sort_order')->orderBy('name')->pluck('name', 'id')->all();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AttendanceAnalyticsOverview::class,
            AttendanceTrendChart::class,
            AttendanceBreakdownChart::class,
            AttendanceServiceComparisonChart::class,
        ];
    }

    public function getHeaderWidgetsData(): array
    {
        return ['year' => $this->year, 'serviceTypeId' => $this->serviceTypeId];
    }
}
