@php
    $user = auth()->user();
    $queries = data_get($searchConsole, 'queries', []);
    $countries = data_get($searchConsole, 'countries', []);
    $clicks = data_get($searchConsole, 'clicks', 0);
    $impressions = data_get($searchConsole, 'impressions', 0);
    $ctr = data_get($searchConsole, 'ctr', 0);
    $position = data_get($searchConsole, 'position', 0);
    $pages = data_get($searchConsole, 'pages', []);
@endphp



<div class="gims-dashboard-responsive" style="display:grid; gap:24px;">

    <div style="background:linear-gradient(135deg,#0f172a,#1d4ed8,#0284c7); border-radius:28px; padding:34px; color:white; box-shadow:0 25px 60px rgba(37,99,235,.25);">
        <div style="display:inline-flex; padding:8px 14px; border-radius:999px; background:rgba(255,255,255,.16); font-weight:800; font-size:13px;">
            👋 Welcome back
        </div>

        <h2 style="font-size:36px; font-weight:900; margin:22px 0 8px;">
            Hello, {{ $user?->name ?? 'Admin' }}
        </h2>

        <p style="max-width:680px; color:rgba(255,255,255,.86); font-size:16px;">
            Manage website content, recruitment posts, blog updates and submitted job applications from one dashboard.
        </p>

        <form method="POST" action="{{ route('filament.admin.auth.logout') }}" style="margin-top:24px;">
            @csrf
            <button type="submit" style="background:#fbbf24; color:#111827; border:0; padding:14px 22px; border-radius:16px; font-weight:900; cursor:pointer;">
                🚪 Sign out
            </button>
        </form>
    </div>

    <div class="gims-stats-responsive" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap:22px;">
        <a href="{{ \App\Filament\Resources\JobListings\JobListingResource::getUrl('index') }}" style="text-decoration:none; background:linear-gradient(135deg,#dbeafe,#eff6ff); border:1px solid #bfdbfe; border-radius:24px; padding:26px; color:#1e3a8a; box-shadow:0 18px 40px rgba(37,99,235,.14);">
            <p style="font-weight:800;">Posted Jobs</p>
            <p style="font-size:36px; font-weight:900; margin:8px 0;">{{ $jobsCount }}</p>
            <p>Manage active job adverts 🚀</p>
        </a>

        <a href="{{ \App\Filament\Resources\JobApplications\JobApplicationResource::getUrl('index') }}" style="text-decoration:none; background:linear-gradient(135deg,#dcfce7,#f0fdf4); border:1px solid #bbf7d0; border-radius:24px; padding:26px; color:#14532d; box-shadow:0 18px 40px rgba(22,163,74,.14);">
            <p style="font-weight:800;">Applicants</p>
            <p style="font-size:36px; font-weight:900; margin:8px 0;">{{ $applicantsCount }}</p>
            <p>Review submitted applications ✅</p>
        </a>

        <a href="{{ \App\Filament\Resources\BlogPosts\BlogPostResource::getUrl('index') }}" style="text-decoration:none; background:linear-gradient(135deg,#fef3c7,#fffbeb); border:1px solid #fde68a; border-radius:24px; padding:26px; color:#78350f; box-shadow:0 18px 40px rgba(245,158,11,.14);">
            <p style="font-weight:800;">Blog Posts</p>
            <p style="font-size:36px; font-weight:900; margin:8px 0;">{{ $blogCount }}</p>
            <p>Publish news and updates ✍️</p>
        </a>
    </div>

    <div style="background:white; border-radius:28px; padding:30px; border:1px solid #e5e7eb; box-shadow:0 24px 60px rgba(15,23,42,.08); color:#0f172a;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap;">
            <div>
                <h2 style="font-size:24px; font-weight:900;">Google Search Console</h2>
                <p style="color:#64748b; margin-top:4px;">Last 90 days website search performance</p>
            </div>

            <a href="https://search.google.com/search-console" target="_blank" style="background:#2563eb; color:white; padding:12px 18px; border-radius:14px; font-weight:900; text-decoration:none;">
                Open Search Console ↗
            </a>
        </div>

       <div class="gims-metrics-responsive" style="margin-top:24px; display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap:18px;">
            <div style="background:#dbeafe; border-radius:20px; padding:22px; color:#1e3a8a;">
                <p style="font-weight:800;">Clicks</p>
                <p style="font-size:32px; font-weight:900;">{{ number_format($clicks) }}</p>
            </div>

            <div style="background:#ede9fe; border-radius:20px; padding:22px; color:#4c1d95;">
                <p style="font-weight:800;">Impressions</p>
                <p style="font-size:32px; font-weight:900;">{{ number_format($impressions) }}</p>
            </div>

            <div style="background:#dcfce7; border-radius:20px; padding:22px; color:#14532d;">
                <p style="font-weight:800;">CTR</p>
                <p style="font-size:32px; font-weight:900;">{{ $ctr }}%</p>
            </div>

            <div style="background:#fef3c7; border-radius:20px; padding:22px; color:#78350f;">
                <p style="font-weight:800;">Avg Position</p>
                <p style="font-size:32px; font-weight:900;">{{ $position }}</p>
            </div>
        </div>

        <div style="margin-top:28px;">
            <h3 style="font-size:18px; font-weight:900;">Top Pages</h3>

            <table style="width:100%; margin-top:14px; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="text-align:left; padding:12px;">Page</th>
                        <th style="text-align:right; padding:12px;">Clicks</th>
                        <th style="text-align:right; padding:12px;">Impressions</th>
                        <th style="text-align:right; padding:12px;">CTR</th>
                        <th style="text-align:right; padding:12px;">Position</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages as $page)
                        <tr style="border-top:1px solid #e5e7eb;">
                            <td style="padding:12px; word-break:break-all;">{{ $page['url'] }}</td>
                            <td style="padding:12px; text-align:right;">{{ number_format($page['clicks']) }}</td>
                            <td style="padding:12px; text-align:right;">{{ number_format($page['impressions']) }}</td>
                            <td style="padding:12px; text-align:right;">{{ $page['ctr'] }}%</td>
                            <td style="padding:12px; text-align:right;">{{ $page['position'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="gims-insights-responsive" style="margin-top:32px; display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap:24px;">
            <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:24px; padding:24px;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                    <div>
                        <h3 style="font-size:20px; font-weight:900; color:#0f172a;">Queries leading to your site</h3>
                        <p style="color:#64748b; margin-top:4px;">Top Google search terms from the last 90 days</p>
                    </div>

                    <a href="https://search.google.com/search-console" target="_blank"
                    style="color:#2563eb; font-weight:800; text-decoration:none;">
                        View more →
                    </a>
                </div>

                <table style="width:100%; margin-top:18px; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid #e5e7eb;">
                            <th style="text-align:left; padding:12px; color:#334155;">Query</th>
                            <th style="text-align:right; padding:12px; color:#334155;">Clicks</th>
                            <th style="text-align:right; padding:12px; color:#334155;">Impressions</th>
                            <th style="text-align:right; padding:12px; color:#334155;">CTR</th>
                            <th style="text-align:right; padding:12px; color:#334155;">Position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queries as $query)
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <td style="padding:12px; color:#0f172a; font-weight:700;">
                                    {{ $query['query'] }}
                                </td>
                                <td style="padding:12px; text-align:right; color:#0f172a;">
                                    {{ number_format($query['clicks']) }}
                                </td>
                                <td style="padding:12px; text-align:right; color:#0f172a;">
                                    {{ number_format($query['impressions']) }}
                                </td>
                                <td style="padding:12px; text-align:right; color:#0f172a;">
                                    {{ $query['ctr'] }}%
                                </td>
                                <td style="padding:12px; text-align:right; color:#0f172a;">
                                    {{ $query['position'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:14px; color:#64748b;">
                                    No query data available yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:24px; padding:24px;">
                <h3 style="font-size:20px; font-weight:900; color:#0f172a;">Top countries</h3>
                <p style="color:#64748b; margin-top:4px;">Search traffic by country</p>

                <div style="margin-top:18px; display:grid; gap:16px;">
                    @forelse($countries as $country)
                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                                <div style="font-weight:800; color:#0f172a;">
                                    <span>{{ $country['flag'] }}</span>
                                    <span>{{ $country['name'] }}</span>
                                </div>

                                <div style="font-weight:900; color:#0f172a;">
                                    {{ $country['percentage'] }}%
                                </div>
                            </div>

                            <div style="height:8px; background:#e5e7eb; border-radius:999px; overflow:hidden; margin-top:8px;">
                                <div style="height:8px; width:{{ max($country['percentage'], 3) }}%; background:#0284c7; border-radius:999px;"></div>
                            </div>

                            <div style="font-size:12px; color:#64748b; margin-top:4px;">
                                {{ number_format($country['clicks']) }} clicks
                            </div>
                        </div>
                    @empty
                        <div style="color:#64748b;">No country data available yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .gims-dashboard-responsive {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    .gims-dashboard-responsive * {
        box-sizing: border-box;
    }

    .gims-dashboard-responsive table {
        width: 100%;
    }

    /* Tablet & Mobile */
    @media (max-width: 768px) {

        .gims-dashboard-responsive {
            gap: 18px !important;
        }

        .gims-dashboard-responsive div[style*="padding:34px"] {
            padding: 24px !important;
            border-radius: 22px !important;
        }

        .gims-dashboard-responsive div[style*="padding:30px"] {
            padding: 20px !important;
            border-radius: 22px !important;
        }

        .gims-dashboard-responsive h2[style*="font-size:36px"] {
            font-size: 28px !important;
            line-height: 1.15 !important;
        }

        .gims-dashboard-responsive p[style*="font-size:32px"],
        .gims-dashboard-responsive p[style*="font-size:36px"] {
            font-size: 28px !important;
        }

        .gims-dashboard-responsive a[href*="search.google.com"] {
            width: 100% !important;
            display: block !important;
            text-align: center !important;
        }

        /* Allow horizontal scrolling for tables */
        .gims-dashboard-responsive table {
            display: block !important;
            overflow-x: auto !important;
            white-space: nowrap !important;
        }
    }

    /* Small Phones */
    @media (max-width: 520px) {

        .gims-dashboard-responsive div[style*="padding:26px"],
        .gims-dashboard-responsive div[style*="padding:24px"],
        .gims-dashboard-responsive div[style*="padding:22px"] {
            padding: 18px !important;
        }

        .gims-dashboard-responsive h2[style*="font-size:36px"] {
            font-size: 24px !important;
        }
    }
</style>