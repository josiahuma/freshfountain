<?php

namespace App\Services;

use Google\Client;
use Google\Service\SearchConsole;
use Google\Service\SearchConsole\SearchAnalyticsQueryRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GoogleSearchConsoleService
{
    public function getDashboardData(): array
    {
        return Cache::remember('gsc_dashboard_data', now()->addHours(6), function () {
            $service = $this->service();

            $siteUrl = env('GOOGLE_SEARCH_CONSOLE_SITE_URL', 'https://gimscare.co.uk/');

            $startDate = now()->subDays(90)->toDateString();
            $endDate = now()->subDays(1)->toDateString();

            $summaryRow = $this->query($service, $siteUrl, $startDate, $endDate, [], 1)[0] ?? null;

            $pages = $this->query($service, $siteUrl, $startDate, $endDate, ['page'], 5);
            $queries = $this->query($service, $siteUrl, $startDate, $endDate, ['query'], 5);
            $countries = $this->query($service, $siteUrl, $startDate, $endDate, ['country'], 5);

            $totalCountryClicks = collect($countries)->sum(fn ($row) => (int) $row->getClicks());

            return [
                'clicks' => (int) ($summaryRow?->getClicks() ?? 0),
                'impressions' => (int) ($summaryRow?->getImpressions() ?? 0),
                'ctr' => round(($summaryRow?->getCtr() ?? 0) * 100, 1),
                'position' => round($summaryRow?->getPosition() ?? 0, 1),

                'pages' => collect($pages)->map(fn ($row) => [
                    'url' => $row->getKeys()[0] ?? '-',
                    'clicks' => (int) $row->getClicks(),
                    'impressions' => (int) $row->getImpressions(),
                    'ctr' => round($row->getCtr() * 100, 1),
                    'position' => round($row->getPosition(), 1),
                ])->values()->all(),

                'queries' => collect($queries)->map(fn ($row) => [
                    'query' => $row->getKeys()[0] ?? '-',
                    'clicks' => (int) $row->getClicks(),
                    'impressions' => (int) $row->getImpressions(),
                    'ctr' => round($row->getCtr() * 100, 1),
                    'position' => round($row->getPosition(), 1),
                ])->values()->all(),

                'countries' => collect($countries)->map(function ($row) use ($totalCountryClicks) {
                    $countryCode = strtoupper((string) ($row->getKeys()[0] ?? ''));

                    $clicks = (int) $row->getClicks();

                    return [
                        'code' => $countryCode,
                        'name' => $this->countryName($countryCode),
                        'flag' => $this->countryFlag($countryCode),
                        'clicks' => $clicks,
                        'percentage' => $totalCountryClicks > 0
                            ? round(($clicks / $totalCountryClicks) * 100)
                            : 0,
                    ];
                })->values()->all(),
            ];
        });
    }

    private function query(
        SearchConsole $service,
        string $siteUrl,
        string $startDate,
        string $endDate,
        array $dimensions = [],
        int $rowLimit = 10
    ): array {
        $request = new SearchAnalyticsQueryRequest();

        $request->setStartDate($startDate);
        $request->setEndDate($endDate);
        $request->setRowLimit($rowLimit);

        if (count($dimensions)) {
            $request->setDimensions($dimensions);
        }

        $response = $service->searchanalytics->query($siteUrl, $request);

        return $response->getRows() ?? [];
    }

    private function service(): SearchConsole
    {
        $client = new Client();

        $credentialsPath = config('services.google_search_console.credentials');

        if (! is_string($credentialsPath) || $credentialsPath === '') {
            throw new \RuntimeException(
                'Google Search Console credentials path is not configured.'
            );
        }

        if (! is_file($credentialsPath)) {
            throw new \RuntimeException(
                "Google Search Console credentials file was not found at: {$credentialsPath}"
            );
        }

        $client->setAuthConfig($credentialsPath);
        $client->addScope(SearchConsole::WEBMASTERS_READONLY);

        return new SearchConsole($client);
    }

    private function countryName(string $code): string
    {
        return match ($code) {
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'IN' => 'India',
            'PK' => 'Pakistan',
            'NG' => 'Nigeria',
            'EG' => 'Egypt',
            'PH' => 'Philippines',
            'CA' => 'Canada',
            'AU' => 'Australia',
            'IE' => 'Ireland',
            default => $code ?: 'Unknown',
        };
    }

    private function countryFlag(string $code): string
    {
        return match ($code) {
            'GB' => '🇬🇧',
            'US' => '🇺🇸',
            'IN' => '🇮🇳',
            'PK' => '🇵🇰',
            'NG' => '🇳🇬',
            'EG' => '🇪🇬',
            'PH' => '🇵🇭',
            'CA' => '🇨🇦',
            'AU' => '🇦🇺',
            'IE' => '🇮🇪',
            default => '🌍',
        };
    }
}