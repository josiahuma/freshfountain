<?php

namespace App\Filament\Widgets;

use App\Support\Access\BackendAccess;

use App\Models\Certificate;
use App\Models\CourseEnrollment;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;

class LearningActivityChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading =
        'Learning activity — last six months';

    protected ?string $description =
        'New enrolments, course completions and certificates issued.';

    protected ?string $pollingInterval = '60s';

    protected ?string $maxHeight = '360px';

    protected function getData(): array
    {
        $months = collect(range(5, 0))
            ->map(
                fn (int $monthsAgo): CarbonImmutable =>
                    CarbonImmutable::now()
                        ->startOfMonth()
                        ->subMonths($monthsAgo)
            );

        $labels = $months
            ->map(
                fn (CarbonImmutable $month): string =>
                    $month->format('M Y')
            )
            ->all();

        $enrolments = $months
            ->map(
                fn (CarbonImmutable $month): int =>
                    CourseEnrollment::query()
                        ->whereBetween(
                            'enrolled_at',
                            [
                                $month->startOfMonth(),
                                $month->endOfMonth(),
                            ]
                        )
                        ->count()
            )
            ->all();

        $completions = $months
            ->map(
                fn (CarbonImmutable $month): int =>
                    CourseEnrollment::query()
                        ->whereBetween(
                            'completed_at',
                            [
                                $month->startOfMonth(),
                                $month->endOfMonth(),
                            ]
                        )
                        ->count()
            )
            ->all();

        $certificates = $months
            ->map(
                fn (CarbonImmutable $month): int =>
                    Certificate::query()
                        ->whereBetween(
                            'issued_at',
                            [
                                $month->startOfMonth(),
                                $month->endOfMonth(),
                            ]
                        )
                        ->count()
            )
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'New enrolments',
                    'data' => $enrolments,
                    'borderColor' => '#2563eb',
                    'backgroundColor' =>
                        'rgba(37, 99, 235, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => 'Course completions',
                    'data' => $completions,
                    'borderColor' => '#059669',
                    'backgroundColor' =>
                        'rgba(5, 150, 105, 0.10)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => 'Certificates issued',
                    'data' => $certificates,
                    'borderColor' => '#d97706',
                    'backgroundColor' =>
                        'rgba(217, 119, 6, 0.10)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
            ],

            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,

            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],

            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],

                'tooltip' => [
                    'enabled' => true,
                ],
            ],

            'scales' => [
                'y' => [
                    'beginAtZero' => true,

                    'ticks' => [
                        'precision' => 0,
                    ],

                    'grid' => [
                        'color' =>
                            'rgba(148, 163, 184, 0.18)',
                    ],
                ],

                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return BackendAccess::canView('learning');
    }
}