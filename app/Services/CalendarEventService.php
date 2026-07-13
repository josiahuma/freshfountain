<?php

namespace App\Services;

use App\Models\CalendarEvent;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CalendarEventService
{
    /**
     * Return all event occurrences within a date range.
     *
     * The end of the range is exclusive.
     */
    public function occurrences(
        CarbonImmutable $rangeStart,
        CarbonImmutable $rangeEnd,
        ?string $category = null
    ): Collection {
        $events = CalendarEvent::query()
            ->internal()
            ->published()
            ->where(function (Builder $query) use (
                $rangeStart,
                $rangeEnd
            ): void {
                /*
                 * Non-recurring events overlapping the requested range.
                 */
                $query
                    ->where(function (Builder $query) use (
                        $rangeStart,
                        $rangeEnd
                    ): void {
                        $query
                            ->where(
                                'recurrence_type',
                                CalendarEvent::RECURRENCE_NONE
                            )
                            ->where(function (Builder $query) use (
                                $rangeStart,
                                $rangeEnd
                            ): void {
                                $query
                                    ->whereBetween('starts_at', [
                                        $rangeStart,
                                        $rangeEnd,
                                    ])
                                    ->orWhereBetween('ends_at', [
                                        $rangeStart,
                                        $rangeEnd,
                                    ])
                                    ->orWhere(function (
                                        Builder $query
                                    ) use (
                                        $rangeStart,
                                        $rangeEnd
                                    ): void {
                                        $query
                                            ->where(
                                                'starts_at',
                                                '<=',
                                                $rangeStart
                                            )
                                            ->where(
                                                'ends_at',
                                                '>=',
                                                $rangeEnd
                                            );
                                    });
                            });
                    })

                    /*
                     * Recurring event series capable of producing an
                     * occurrence during the requested range.
                     */
                    ->orWhere(function (Builder $query) use (
                        $rangeStart,
                        $rangeEnd
                    ): void {
                        $query
                            ->where(
                                'recurrence_type',
                                '!=',
                                CalendarEvent::RECURRENCE_NONE
                            )
                            ->where('starts_at', '<', $rangeEnd)
                            ->where(function (Builder $query) use (
                                $rangeStart
                            ): void {
                                $query
                                    ->whereNull('recurrence_until')
                                    ->orWhereDate(
                                        'recurrence_until',
                                        '>=',
                                        $rangeStart->toDateString()
                                    );
                            });
                    });
            })
            ->when(
                filled($category)
                && array_key_exists(
                    $category,
                    CalendarEvent::categoryOptions()
                ),
                fn (Builder $query): Builder =>
                    $query->where('category', $category)
            )
            ->orderBy('starts_at')
            ->orderBy('sort_order')
            ->get();

        return $events
            ->flatMap(
                fn (CalendarEvent $event): Collection =>
                    $this->expandEvent(
                        $event,
                        $rangeStart,
                        $rangeEnd
                    )
            )
            ->sortBy('start')
            ->values();
    }

    /**
     * Build the month structure used by the printable calendar.
     */
    public function printableMonth(
        CarbonImmutable $month
    ): array {
        $monthStart = $month->startOfMonth();
        $monthEnd = $month->endOfMonth();

        /*
         * Calendar weeks begin on Monday.
         */
        $gridStart = $monthStart->startOfWeek(
            CarbonImmutable::MONDAY
        );

        $gridEnd = $monthEnd->endOfWeek(
            CarbonImmutable::SUNDAY
        );

        $occurrences = $this->occurrences(
            $gridStart,
            $gridEnd->addDay()
        );

        $eventsByDate = $occurrences->groupBy(
            fn (array $event): string =>
                CarbonImmutable::parse(
                    $event['start']
                )->toDateString()
        );

        $days = collect();

        $cursor = $gridStart;

        while ($cursor->lessThanOrEqualTo($gridEnd)) {
            $dateKey = $cursor->toDateString();

            $days->push([
                'date' => $cursor,
                'dateKey' => $dateKey,
                'dayNumber' => $cursor->day,
                'isCurrentMonth' => $cursor->month === $month->month,
                'isToday' => $cursor->isToday(),
                'events' => $eventsByDate->get(
                    $dateKey,
                    collect()
                ),
            ]);

            $cursor = $cursor->addDay();
        }

        return [
            'month' => $monthStart,
            'monthLabel' => $monthStart->format('F Y'),
            'previousMonth' => $monthStart
                ->subMonth()
                ->format('Y-m'),
            'nextMonth' => $monthStart
                ->addMonth()
                ->format('Y-m'),
            'weeks' => $days
                ->chunk(7)
                ->values(),
            'eventCount' => $occurrences
                ->filter(function (array $event) use (
                    $monthStart,
                    $monthEnd
                ): bool {
                    $start = CarbonImmutable::parse(
                        $event['start']
                    );

                    return $start->betweenIncluded(
                        $monthStart,
                        $monthEnd->endOfDay()
                    );
                })
                ->count(),
        ];
    }

    /**
     * Build a chronological portrait list for a selected month.
     */
    public function printableListMonth(
        CarbonImmutable $month
    ): array {
        $monthStart = $month->startOfMonth();

        /*
        * The range end is exclusive.
        */
        $monthEnd = $monthStart->addMonth();

        $events = $this->occurrences(
            $monthStart,
            $monthEnd
        )
            ->map(function (array $event): array {
                $start = CarbonImmutable::parse(
                    $event['start']
                );

                $end = filled($event['end'] ?? null)
                    ? CarbonImmutable::parse($event['end'])
                    : null;

                $details = $event['extendedProps'] ?? [];

                return [
                    'id' => $event['id'],
                    'title' => $event['title'],
                    'start' => $start,
                    'end' => $end,
                    'dayName' => $start->format('l'),
                    'dayNumber' => $start->format('d'),
                    'monthShort' => strtoupper(
                        $start->format('M')
                    ),
                    'dateLabel' => $start->format(
                        'l, j F Y'
                    ),
                    'timeLabel' => $details['timeLabel']
                        ?? (
                            $event['allDay']
                                ? 'All day'
                                : $start->format('g:i A')
                        ),
                    'location' => $details['location']
                        ?? null,
                    'description' => $details['description']
                        ?? null,
                    'categoryLabel' =>
                        $details['categoryLabel']
                        ?? 'Event',
                    'colour' => $details['colour']
                        ?? '#1d4ed8',
                    'isRecurring' =>
                        $details['isRecurring']
                        ?? false,
                    'externalUrl' =>
                        $details['externalUrl']
                        ?? null,
                ];
            })
            ->sortBy('start')
            ->values();

        return [
            'month' => $monthStart,
            'monthLabel' => $monthStart->format('F Y'),
            'previousMonth' => $monthStart
                ->subMonth()
                ->format('Y-m'),
            'nextMonth' => $monthStart
                ->addMonth()
                ->format('Y-m'),
            'events' => $events,
            'eventCount' => $events->count(),
        ];
    }

    private function expandEvent(
        CalendarEvent $event,
        CarbonImmutable $rangeStart,
        CarbonImmutable $rangeEnd
    ): Collection {
        $recurrenceType = $event->recurrence_type
            ?: CalendarEvent::RECURRENCE_NONE;

        $baseStart = CarbonImmutable::parse(
            $event->starts_at
        );

        $baseEnd = $event->ends_at
            ? CarbonImmutable::parse($event->ends_at)
            : null;

        $durationSeconds = $baseEnd
            ? max(
                0,
                $baseStart->diffInSeconds(
                    $baseEnd
                )
            )
            : null;

        if (
            $recurrenceType
            === CalendarEvent::RECURRENCE_NONE
        ) {
            if (
                ! $this->overlapsRange(
                    $baseStart,
                    $baseEnd,
                    $rangeStart,
                    $rangeEnd
                )
            ) {
                return collect();
            }

            return collect([
                $this->formatOccurrence(
                    $event,
                    $baseStart,
                    $baseEnd,
                    false
                ),
            ]);
        }

        $recurrenceLimit = $event->recurrence_until
            ? CarbonImmutable::parse(
                $event->recurrence_until
            )->endOfDay()
            : $rangeEnd;

        $effectiveEnd = $recurrenceLimit->lessThan(
            $rangeEnd
        )
            ? $recurrenceLimit
            : $rangeEnd;

        $starts = match ($recurrenceType) {
            CalendarEvent::RECURRENCE_DAILY =>
                $this->dailyOccurrences(
                    $baseStart,
                    $rangeStart,
                    $effectiveEnd
                ),

            CalendarEvent::RECURRENCE_WEEKLY =>
                $this->weeklyOccurrences(
                    $baseStart,
                    $rangeStart,
                    $effectiveEnd
                ),

            CalendarEvent::RECURRENCE_MONTHLY =>
                $this->monthlyOccurrences(
                    $baseStart,
                    $rangeStart,
                    $effectiveEnd
                ),

            CalendarEvent::RECURRENCE_YEARLY =>
                $this->yearlyOccurrences(
                    $baseStart,
                    $rangeStart,
                    $effectiveEnd
                ),

            default => collect([$baseStart]),
        };

        return $starts
            ->filter(
                fn (CarbonImmutable $start): bool =>
                    $start->greaterThanOrEqualTo($baseStart)
                    && $start->lessThan($rangeEnd)
                    && $start->lessThanOrEqualTo(
                        $recurrenceLimit
                    )
            )
            ->map(function (
                CarbonImmutable $occurrenceStart
            ) use (
                $event,
                $durationSeconds
            ): array {
                $occurrenceEnd = $durationSeconds !== null
                    ? $occurrenceStart->addSeconds(
                        $durationSeconds
                    )
                    : null;

                return $this->formatOccurrence(
                    $event,
                    $occurrenceStart,
                    $occurrenceEnd,
                    true
                );
            })
            ->filter(function (
                array $occurrence
            ) use (
                $rangeStart,
                $rangeEnd
            ): bool {
                return $this->overlapsRange(
                    CarbonImmutable::parse(
                        $occurrence['start']
                    ),
                    filled($occurrence['end'])
                        ? CarbonImmutable::parse(
                            $occurrence['end']
                        )
                        : null,
                    $rangeStart,
                    $rangeEnd
                );
            })
            ->values();
    }

    private function dailyOccurrences(
        CarbonImmutable $baseStart,
        CarbonImmutable $rangeStart,
        CarbonImmutable $rangeEnd
    ): Collection {
        $cursor = $baseStart;

        if ($cursor->lessThan($rangeStart)) {
            $daysToSkip = $cursor
                ->startOfDay()
                ->diffInDays(
                    $rangeStart->startOfDay()
                );

            $cursor = $cursor->addDays(
                $daysToSkip
            );

            while ($cursor->lessThan($rangeStart)) {
                $cursor = $cursor->addDay();
            }
        }

        $occurrences = collect();

        while ($cursor->lessThan($rangeEnd)) {
            $occurrences->push($cursor);
            $cursor = $cursor->addDay();
        }

        return $occurrences;
    }

    private function weeklyOccurrences(
        CarbonImmutable $baseStart,
        CarbonImmutable $rangeStart,
        CarbonImmutable $rangeEnd
    ): Collection {
        $cursor = $baseStart;

        if ($cursor->lessThan($rangeStart)) {
            $daysDifference = $cursor
                ->startOfDay()
                ->diffInDays(
                    $rangeStart->startOfDay()
                );

            $weeksToSkip = intdiv(
                $daysDifference,
                7
            );

            $cursor = $cursor->addWeeks(
                $weeksToSkip
            );

            while ($cursor->lessThan($rangeStart)) {
                $cursor = $cursor->addWeek();
            }
        }

        $occurrences = collect();

        while ($cursor->lessThan($rangeEnd)) {
            $occurrences->push($cursor);
            $cursor = $cursor->addWeek();
        }

        return $occurrences;
    }

    /**
     * Monthly recurrence uses the weekday position represented by the
     * original event date.
     *
     * Example: first Friday, second Tuesday, fourth Sunday.
     */
    private function monthlyOccurrences(
        CarbonImmutable $baseStart,
        CarbonImmutable $rangeStart,
        CarbonImmutable $rangeEnd
    ): Collection {
        $weekday = $baseStart->dayOfWeek;

        $weekOfMonth = intdiv(
            $baseStart->day - 1,
            7
        ) + 1;

        $cursorMonth = $baseStart->startOfMonth();

        if (
            $cursorMonth->lessThan(
                $rangeStart->startOfMonth()
            )
        ) {
            $cursorMonth = $rangeStart
                ->startOfMonth();
        }

        $occurrences = collect();

        while ($cursorMonth->lessThan($rangeEnd)) {
            $firstDay = $cursorMonth->startOfMonth();

            $daysUntilWeekday = (
                $weekday
                - $firstDay->dayOfWeek
                + 7
            ) % 7;

            $candidate = $firstDay
                ->addDays($daysUntilWeekday)
                ->addWeeks($weekOfMonth - 1)
                ->setTime(
                    $baseStart->hour,
                    $baseStart->minute,
                    $baseStart->second
                );

            if (
                $candidate->month
                === $cursorMonth->month
            ) {
                $occurrences->push($candidate);
            }

            $cursorMonth = $cursorMonth->addMonth();
        }

        return $occurrences;
    }

    private function yearlyOccurrences(
        CarbonImmutable $baseStart,
        CarbonImmutable $rangeStart,
        CarbonImmutable $rangeEnd
    ): Collection {
        $year = max(
            $baseStart->year,
            $rangeStart->year
        );

        $occurrences = collect();

        while ($year <= $rangeEnd->year) {
            if (
                checkdate(
                    $baseStart->month,
                    $baseStart->day,
                    $year
                )
            ) {
                $occurrences->push(
                    $baseStart->setYear($year)
                );
            }

            $year++;
        }

        return $occurrences;
    }

    private function overlapsRange(
        CarbonImmutable $start,
        ?CarbonImmutable $end,
        CarbonImmutable $rangeStart,
        CarbonImmutable $rangeEnd
    ): bool {
        $effectiveEnd = $end ?: $start;

        return $start->lessThan($rangeEnd)
            && $effectiveEnd->greaterThanOrEqualTo(
                $rangeStart
            );
    }

    private function formatOccurrence(
        CalendarEvent $event,
        CarbonImmutable $start,
        ?CarbonImmutable $end,
        bool $isRecurring
    ): array {
        $calendarEnd = null;

        if ($event->is_all_day) {
            $calendarEnd = ($end ?: $start)
                ->startOfDay()
                ->addDay()
                ->toDateString();
        } elseif ($end) {
            $calendarEnd = $end->toIso8601String();
        }

        return [
            'id' => sprintf(
                'internal-%d-%s',
                $event->id,
                $start->format('YmdHis')
            ),

            'groupId' => 'internal-series-' . $event->id,

            'title' => $event->title,

            'start' => $event->is_all_day
                ? $start->toDateString()
                : $start->toIso8601String(),

            'end' => $calendarEnd,

            'allDay' => $event->is_all_day,

            'backgroundColor' =>
                $event->colour ?: '#1d4ed8',

            'borderColor' =>
                $event->colour ?: '#1d4ed8',

            'textColor' => '#ffffff',

            'extendedProps' => [
                'databaseId' => $event->id,

                'description' => trim(
                    strip_tags(
                        $event->description ?? ''
                    )
                ),

                'location' => $event->location,

                'category' => $event->category,

                'categoryLabel' =>
                    CalendarEvent::categoryOptions()[
                        $event->category
                    ] ?? ucfirst($event->category),

                'colour' =>
                    $event->colour ?: '#1d4ed8',

                'imageUrl' => $event->image_url,

                'externalUrl' =>
                    $event->external_url,

                'isFeatured' =>
                    $event->is_featured,

                'isRecurring' =>
                    $isRecurring,

                'recurrenceType' =>
                    $event->recurrence_type,

                'source' =>
                    CalendarEvent::SOURCE_INTERNAL,

                'dateLabel' => $event->is_all_day
                    ? $start->format('j F Y')
                    : $start->format(
                        'j F Y \a\t g:i A'
                    ),

                'timeLabel' => $event->is_all_day
                    ? 'All day'
                    : (
                        $end
                            ? $start->format('g:i A')
                                . ' – '
                                . $end->format('g:i A')
                            : $start->format('g:i A')
                    ),
            ],
        ];
    }
}