<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
use App\Support\Access\BackendAccess;
use Carbon\CarbonImmutable;
use Filament\Widgets\Widget;

class UpcomingBirthdays extends Widget
{
    protected string $view =
        'filament.widgets.upcoming-birthdays';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        $today = CarbonImmutable::today();

        $members = Member::query()
            ->active()
            ->upcomingBirthdays(0, 30)
            ->get()
            ->filter(
                fn (Member $member): bool =>
                    $member->next_birthday !== null
            )
            ->sortBy(
                fn (Member $member): string =>
                    $member->next_birthday->format('Y-m-d')
            )
            ->values();

        $todayBirthdays = $members
            ->filter(
                fn (Member $member): bool =>
                    $member->next_birthday->isSameDay($today)
            )
            ->values();

        $nextSevenDays = $members
            ->filter(
                function (Member $member) use ($today): bool {
                    $days = $today->diffInDays(
                        $member->next_birthday,
                        false
                    );

                    return $days >= 1 && $days <= 7;
                }
            )
            ->values();

        $comingUp = $members
            ->filter(
                function (Member $member) use ($today): bool {
                    $days = $today->diffInDays(
                        $member->next_birthday,
                        false
                    );

                    return $days >= 8 && $days <= 30;
                }
            )
            ->values();

        return [
            'todayBirthdays' => $todayBirthdays,
            'nextSevenDays' => $nextSevenDays,
            'comingUp' => $comingUp,
            'hasBirthdays' => $members->isNotEmpty(),
            'membersUrl' => MemberResource::getUrl(
                'index',
                [
                    'tableFilters' => [
                        'birthday' => [
                            'value' => 'next_30_days',
                        ],
                    ],
                ]
            ),
        ];
    }

    public static function canView(): bool
    {
        return BackendAccess::canView('members');
    }
}
