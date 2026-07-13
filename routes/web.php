<?php

use App\Http\Controllers\Admin\JobApplicationPdfController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\SitemapController;
use App\Models\BlogPost;
use App\Models\Page;
use Illuminate\Support\Facades\Route;
use App\Services\CalendarEventService;
use Carbon\CarbonImmutable;

function pageViewFor(Page $page): string
{
    return match ($page->template) {
        'home' => 'pages.home',
        'service' => 'pages.service',
        'services_index' => 'pages.services_index',
        'about' => 'pages.about',
        'leaders' => 'pages.leaders',
        'contact' => 'pages.contact',
        'jobs' => 'pages.jobs',
        'blog' => 'pages.blog',
        'course' => 'pages.course',
        'courses_index' => 'pages.courses_index',
        'units' => 'pages.units',
        'units_index' => 'pages.units_index',
        'giving' => 'pages.giving',
        default => 'pages.service',
    };
}

/*
|--------------------------------------------------------------------------
| Homepage
|--------------------------------------------------------------------------
*/

Route::get('/', function (CalendarEventService $calendarService) {
    $page = Page::query()
        ->where('slug', 'home')
        ->where('is_published', true)
        ->firstOrFail();

    /*
    |--------------------------------------------------------------------------
    | Upcoming website calendar events
    |--------------------------------------------------------------------------
    |
    | Recurring events are expanded automatically by CalendarEventService.
    | We load the next four months, then display the first six occurrences.
    |
    */

    $rangeStart = CarbonImmutable::now()->startOfDay();
    $rangeEnd = $rangeStart->addMonth();

    $calendarEvents = $calendarService
        ->occurrences($rangeStart, $rangeEnd)
        ->filter(function (array $event) use ($rangeStart): bool {
            return CarbonImmutable::parse($event['start'])
                ->greaterThanOrEqualTo($rangeStart);
        })
        ->take(6)
        ->values();

    return view(
        pageViewFor($page),
        compact('page', 'calendarEvents')
    );
});

/*
|--------------------------------------------------------------------------
| Careers and job portal
|--------------------------------------------------------------------------
*/

Route::get('/careers', [CareerController::class, 'index'])
    ->name('careers.index');

Route::get('/careers/{slug}', [CareerController::class, 'show'])
    ->name('careers.show');

Route::get('/careers/{slug}/apply', [CareerController::class, 'apply'])
    ->name('careers.apply');

Route::post('/careers/{slug}/apply', [CareerController::class, 'submit'])
    ->name('careers.submit');

Route::get('/careers/{slug}/success', [CareerController::class, 'success'])
    ->name('careers.success');

/*
|--------------------------------------------------------------------------
| Public calendar
|--------------------------------------------------------------------------
*/

Route::get('/calendar/feed', [CalendarController::class, 'feed'])
    ->name('calendar.feed');

Route::get('/calendar/print', [CalendarController::class, 'print'])
    ->name('calendar.print');

Route::get('/calendar/pdf', [CalendarController::class, 'pdf'])
    ->name('calendar.pdf');

Route::get('/calendar', [CalendarController::class, 'index'])
    ->name('calendar.index');

/*
|--------------------------------------------------------------------------
| Sitemap
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index'])
    ->name('sitemap');

/*
|--------------------------------------------------------------------------
| Job application PDF
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/job-applications/{jobApplication}/pdf',
    [JobApplicationPdfController::class, 'show']
)->name('admin.job-applications.pdf');

/*
|--------------------------------------------------------------------------
| Individual blog posts
|--------------------------------------------------------------------------
*/

Route::get('/blog/{slug}', function (string $slug) {
    $post = BlogPost::published()
        ->where('slug', $slug)
        ->firstOrFail();

    return view('blog.show', compact('post'));
})
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('blog.show');

/*
|--------------------------------------------------------------------------
| Debug upload limits
|--------------------------------------------------------------------------
*/

Route::get('/_debug-upload-limits', function () {
    return response()->json([
        'post_max_size' => ini_get('post_max_size'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'memory_limit' => ini_get('memory_limit'),
    ]);
});

/*
|--------------------------------------------------------------------------
| Catch-all CMS pages
|--------------------------------------------------------------------------
|
| This route must remain last.
|
*/

Route::get('/{slug}', function (string $slug) {
    $page = Page::query()
        ->where('slug', $slug)
        ->where('is_published', true)
        ->firstOrFail();

    return view(pageViewFor($page), compact('page'));
})
    ->where(
        'slug',
        '^(?!admin$|_debug-upload-limits$|careers$|calendar$|sitemap\.xml$).+'
    )
    ->name('pages.show');