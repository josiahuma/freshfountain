<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int|array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            DashboardOverview::class,
        ];
    }
}