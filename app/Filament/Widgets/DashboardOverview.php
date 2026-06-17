<?php

namespace App\Filament\Widgets;

use App\Models\BlogPost;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Services\GoogleSearchConsoleService;
use Filament\Widgets\Widget;

class DashboardOverview extends Widget
{
    protected string $view = 'filament.widgets.dashboard-overview';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $searchConsole = app(GoogleSearchConsoleService::class)->getDashboardData();

        return [
            'jobsCount' => JobListing::count(),
            'applicantsCount' => JobApplication::count(),
            'blogCount' => BlogPost::count(),
            'searchConsole' => $searchConsole,
        ];
    }
}