<?php

namespace App\Filament\Widgets;

use App\Support\Access\BackendAccess;

use App\Models\FinanceTransaction;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;

class FinanceIncomeExpenseChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading =
        'Income vs expenses — last six months';

    protected ?string $description =
        'Completed finance transactions grouped by month.';

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

        $income = $months
            ->map(
                fn (CarbonImmutable $month): float =>
                    (float) FinanceTransaction::query()
                        ->where(
                            'type',
                            FinanceTransaction::TYPE_INCOME
                        )
                        ->where(
                            'status',
                            FinanceTransaction::STATUS_COMPLETED
                        )
                        ->whereBetween(
                            'transaction_date',
                            [
                                $month->startOfMonth()->toDateString(),
                                $month->endOfMonth()->toDateString(),
                            ]
                        )
                        ->sum('amount')
            )
            ->all();

        $expenses = $months
            ->map(
                fn (CarbonImmutable $month): float =>
                    (float) FinanceTransaction::query()
                        ->where(
                            'type',
                            FinanceTransaction::TYPE_EXPENSE
                        )
                        ->where(
                            'status',
                            FinanceTransaction::STATUS_COMPLETED
                        )
                        ->whereBetween(
                            'transaction_date',
                            [
                                $month->startOfMonth()->toDateString(),
                                $month->endOfMonth()->toDateString(),
                            ]
                        )
                        ->sum('amount')
            )
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $income,
                    'borderColor' => '#059669',
                    'backgroundColor' =>
                        'rgba(5, 150, 105, 0.16)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => 'Expenses',
                    'data' => $expenses,
                    'borderColor' => '#dc2626',
                    'backgroundColor' =>
                        'rgba(220, 38, 38, 0.12)',
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

                    'callbacks' => [
                        'label' => "function(context) {
                            return context.dataset.label
                                + ': £'
                                + Number(context.raw).toLocaleString(
                                    'en-GB',
                                    {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }
                                );
                        }",
                    ],
                ],
            ],

            'scales' => [
                'y' => [
                    'beginAtZero' => true,

                    'ticks' => [
                        'callback' => "function(value) {
                            return '£' + Number(value).toLocaleString('en-GB');
                        }",
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
        return BackendAccess::canView('finance');
    }
}
