<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Attendances\AttendanceResource;
use App\Models\Attendance;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class AttendanceAnalyticsOverview extends StatsOverviewWidget
{
    public ?int $year = null;
    public ?int $serviceTypeId = null;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = 'Attendance overview';
    protected ?string $description = 'Numbers-only service attendance insights.';
    protected function getColumns(): int { return 4; }

    protected function getStats(): array
    {
        $query = $this->baseQuery();
        $count = (clone $query)->count();
        $sum = (int) (clone $query)->sum('total');
        $average = $count > 0 ? round($sum / $count, 1) : 0;
        $highest = (clone $query)->orderByDesc('total')->first();
        $lowest = (clone $query)->where('total', '>', 0)->orderBy('total')->first();
        $latest = (clone $query)->latest('service_date')->first();
        $visitors = (int) (clone $query)->sum('visitors');
        $online = (int) (clone $query)->sum('online');
        $growth = $this->growthPercentage();
        $url = AttendanceResource::getUrl('index');

        return [
            Stat::make('Latest attendance', number_format((int) ($latest?->total ?? 0)))
                ->description($latest ? $latest->service_name.' · '.$latest->service_date->format('d M Y') : 'No records')
                ->descriptionIcon('heroicon-m-calendar-days')->color('info')->url($url),
            Stat::make('Average attendance', number_format($average, 1))
                ->description(number_format($count).' services in selection')->descriptionIcon('heroicon-m-chart-bar')->color('primary')->url($url),
            Stat::make('Highest attendance', number_format((int) ($highest?->total ?? 0)))
                ->description($highest ? $highest->service_name.' · '.$highest->service_date->format('d M Y') : 'No records')
                ->descriptionIcon('heroicon-m-arrow-trending-up')->color('success')->url($url),
            Stat::make('Lowest attendance', number_format((int) ($lowest?->total ?? 0)))
                ->description($lowest ? $lowest->service_name.' · '.$lowest->service_date->format('d M Y') : 'No records')
                ->descriptionIcon('heroicon-m-arrow-trending-down')->color('warning')->url($url),
            Stat::make('Visitors', number_format($visitors))->description('Visitors recorded in selection')->descriptionIcon('heroicon-m-user-plus')->color('success')->url($url),
            Stat::make('Online attendance', number_format($online))->description('Online viewers recorded')->descriptionIcon('heroicon-m-video-camera')->color('info')->url($url),
            Stat::make('Total attendance', number_format($sum))->description('Combined attendance across services')->descriptionIcon('heroicon-m-users')->color('primary')->url($url),
            Stat::make('Recent growth', ($growth >= 0 ? '+' : '').number_format($growth, 1).'%')
                ->description('Last 6 services vs previous 6')->descriptionIcon($growth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')->color($growth >= 0 ? 'success' : 'danger')->url($url),
        ];
    }

    private function baseQuery(): Builder
    {
        return Attendance::query()
            ->when($this->year, fn (Builder $q) => $q->whereYear('service_date', $this->year))
            ->when($this->serviceTypeId, fn (Builder $q) => $q->where('service_type_id', $this->serviceTypeId));
    }

    private function growthPercentage(): float
    {
        $rows = $this->baseQuery()->orderByDesc('service_date')->limit(12)->pluck('total')->map(fn ($v) => (float) $v)->values();
        if ($rows->count() < 4) return 0;
        $half = intdiv($rows->count(), 2);
        $recent = $rows->take($half)->avg();
        $previous = $rows->slice($half)->avg();
        return $previous > 0 ? (($recent - $previous) / $previous) * 100 : 0;
    }
}
