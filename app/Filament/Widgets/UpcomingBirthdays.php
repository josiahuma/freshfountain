<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
use App\Models\SmsTemplate;
use App\Services\Messaging\SmsTemplateService;
use App\Support\Access\BackendAccess;
use Carbon\CarbonImmutable;
use Filament\Widgets\Widget;
use Filament\Notifications\Notification;

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

    public function sendBirthdaySms(): void
    {
        $template = SmsTemplate::query()
            ->where('is_active', true)
            ->where('is_birthday', true)
            ->latest('updated_at')
            ->first();

        if (! $template) {
            Notification::make()
                ->title('No birthday SMS template')
                ->body('Create or activate an SMS template and mark it as the Birthday template first.')
                ->warning()
                ->send();
            return;
        }

        $today = CarbonImmutable::today();
        $members = Member::query()
            ->active()
            ->upcomingBirthdays(0, 0)
            ->get()
            ->filter(fn (Member $member): bool => $member->next_birthday?->isSameDay($today) === true)
            ->values();

        if ($members->isEmpty()) {
            Notification::make()->title('No birthdays today')->info()->send();
            return;
        }

        $summary = app(SmsTemplateService::class)->sendToMembers($members, $template);

        Notification::make()
            ->title('Birthday SMS sending complete')
            ->body("Sent: {$summary['sent']} · Skipped: {$summary['skipped']} · Failed: {$summary['failed']}")
            ->success()
            ->send();
    }

    public static function canView(): bool
    {
        return BackendAccess::canView('members');
    }
}
