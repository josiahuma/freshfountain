<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\FinanceAnalyticsOverview;
use App\Filament\Widgets\FinanceIncomeExpenseChart;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class FinanceAnalytics extends Page
{
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedChartBarSquare;

    protected static string|UnitEnum|null $navigationGroup =
        'Finance';

    protected static ?string $navigationLabel =
        'Finance Analytics';

    protected static ?string $title =
        'Finance Analytics';

    protected static ?string $slug =
        'finance-analytics';

    protected static ?int $navigationSort = 6;

    protected string $view =
        'filament.pages.finance-analytics';

    public function getColumns(): int|array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FinanceAnalyticsOverview::class,
            FinanceIncomeExpenseChart::class,
        ];
    }
}
