<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Attendances\AttendanceResource;
use App\Filament\Resources\CalendarEvents\CalendarEventResource;
use App\Filament\Resources\ChurchUnits\ChurchUnitResource;
use App\Filament\Resources\Leaders\LeaderResource;
use App\Filament\Resources\Members\MemberResource;
use App\Models\Attendance;
use App\Models\ChurchUnit;
use App\Models\Leader;
use App\Models\Member;
use App\Services\CalendarEventService;
use App\Support\Access\BackendAccess;
use Carbon\CarbonImmutable;
use Filament\Widgets\Widget;

class DashboardOverview extends Widget
{
    protected string $view = 'filament.widgets.dashboard-overview';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $today = CarbonImmutable::today();

        $canViewMembers = BackendAccess::canView('members');
        $canViewAttendance = BackendAccess::canView('attendance');
        $canViewCalendar = BackendAccess::canView('calendar');
        $canViewUnits = BackendAccess::canView('church_units');
        $canViewLeaders = BackendAccess::canView('leaders');

        $activeMembers = $canViewMembers
            ? Member::query()->active()->count()
            : null;

        $newMembers = $canViewMembers
            ? Member::query()
                ->active()
                ->whereNotNull('joined_at')
                ->whereDate('joined_at', '>=', $today->subDays(30))
                ->count()
            : null;

        $latestAttendance = null;
        $previousAttendance = null;
        $attendanceChange = null;
        $recentAttendances = collect();

        if ($canViewAttendance) {
            $recentAttendances = Attendance::query()
                ->whereDate('service_date', '<=', $today)
                ->orderByDesc('service_date')
                ->orderByDesc('id')
                ->limit(8)
                ->get();

            $latestAttendance = $recentAttendances->get(0);
            $previousAttendance = $recentAttendances->get(1);

            if ($latestAttendance && $previousAttendance && (int) $previousAttendance->total > 0) {
                $attendanceChange = round(
                    (((int) $latestAttendance->total - (int) $previousAttendance->total)
                        / (int) $previousAttendance->total) * 100,
                    1
                );
            }
        }

        $upcomingEvents = collect();
        $upcomingEventCount = null;

        if ($canViewCalendar) {
            $eventService = app(CalendarEventService::class);

            $nextThirtyDays = $eventService->occurrences(
                $today,
                $today->addDays(30)->endOfDay()
            );

            $upcomingEventCount = $nextThirtyDays->count();

            $upcomingEvents = $eventService->occurrences(
                $today,
                $today->addDays(60)->endOfDay()
            )
                ->take(5)
                ->values();
        }

        $activeUnits = $canViewUnits
            ? ChurchUnit::query()->active()->count()
            : null;

        $activeLeaders = $canViewLeaders
            ? Leader::query()->active()->count()
            : null;

        $visitorsThisMonth = $canViewAttendance
            ? (int) Attendance::query()
                ->whereBetween('service_date', [
                    $today->startOfMonth(),
                    $today->endOfMonth(),
                ])
                ->sum('visitors')
            : null;

        $totalAttendanceThisMonth = $canViewAttendance
            ? (int) Attendance::query()
                ->whereBetween('service_date', [
                    $today->startOfMonth(),
                    $today->endOfMonth(),
                ])
                ->sum('total')
            : null;

        return [
            'activeMembers' => $activeMembers,
            'newMembers' => $newMembers,
            'latestAttendance' => $latestAttendance,
            'previousAttendance' => $previousAttendance,
            'attendanceChange' => $attendanceChange,
            'recentAttendances' => $recentAttendances,
            'upcomingEvents' => $upcomingEvents,
            'upcomingEventCount' => $upcomingEventCount,
            'activeUnits' => $activeUnits,
            'activeLeaders' => $activeLeaders,
            'visitorsThisMonth' => $visitorsThisMonth,
            'totalAttendanceThisMonth' => $totalAttendanceThisMonth,
            'canViewMembers' => $canViewMembers,
            'canViewAttendance' => $canViewAttendance,
            'canViewCalendar' => $canViewCalendar,
            'canViewUnits' => $canViewUnits,
            'canViewLeaders' => $canViewLeaders,
            'membersUrl' => $canViewMembers ? MemberResource::getUrl('index') : null,
            'attendanceUrl' => $canViewAttendance ? AttendanceResource::getUrl('index') : null,
            'calendarUrl' => $canViewCalendar ? CalendarEventResource::getUrl('index') : null,
            'unitsUrl' => $canViewUnits ? ChurchUnitResource::getUrl('index') : null,
            'leadersUrl' => $canViewLeaders ? LeaderResource::getUrl('index') : null,
        ];
    }
}
