<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LearningActivityChart;
use App\Filament\Widgets\LearningAnalyticsOverview;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LearningAnalytics extends Page
{
    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedChartBarSquare;

    protected static string|UnitEnum|null $navigationGroup =
        'Learning Management';

    protected static ?string $navigationLabel =
        'Learning Analytics';

    protected static ?string $title =
        'Learning Analytics';

    protected static ?string $slug =
        'learning-analytics';

    protected static ?int $navigationSort = 3;

    protected string $view =
        'filament.pages.learning-analytics';

    public function getColumns(): int|array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LearningAnalyticsOverview::class,
            LearningActivityChart::class,
        ];
    }
}