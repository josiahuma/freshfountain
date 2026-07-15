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
use App\Http\Controllers\Auth\MemberAuthController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\CertificateController;

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
| Member authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {
    Route::get(
        '/login',
        [MemberAuthController::class, 'showLogin']
    )->name('login');

    Route::post(
        '/login',
        [MemberAuthController::class, 'login']
    )->name('login.store');

    Route::get(
        '/register',
        [MemberAuthController::class, 'showRegister']
    )->name('register');

    Route::post(
        '/register',
        [MemberAuthController::class, 'register']
    )->name('register.store');
});

Route::post(
    '/logout',
    [MemberAuthController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Member learning portal
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('learn')
    ->name('learn.')
    ->scopeBindings()
    ->group(function (): void {
        Route::get(
            '/',
            [LearningController::class, 'dashboard']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Access restriction notice
        |--------------------------------------------------------------------------
        |
        | This route must not use course.access, otherwise it would redirect
        | back to itself continuously.
        |
        */

        Route::get(
            '/courses/{course:slug}/access-restricted',
            [
                LearningController::class,
                'accessRestricted',
            ]
        )->name(
            'courses.access-restricted'
        );

        Route::middleware('course.access')
            ->group(function (): void {
                Route::get(
                    '/courses/{course:slug}',
                    [
                        LearningController::class,
                        'course',
                    ]
                )->name('courses.show');

                Route::post(
                    '/courses/{course:slug}/enrol',
                    [
                        LearningController::class,
                        'enroll',
                    ]
                )->name('courses.enroll');

                Route::get(
                    '/courses/{course:slug}/lessons/{lesson:slug}',
                    [
                        LearningController::class,
                        'lesson',
                    ]
                )->name('lessons.show');

                Route::get(
                    '/courses/{course:slug}/lessons/{lesson:slug}/quiz',
                    [
                        LearningController::class,
                        'quiz',
                    ]
                )->name('quiz.show');

                Route::post(
                    '/courses/{course:slug}/lessons/{lesson:slug}/quiz',
                    [
                        LearningController::class,
                        'submitQuiz',
                    ]
                )->name('quiz.submit');

                Route::get(
                    '/courses/{course:slug}/lessons/{lesson:slug}/quiz/results/{attempt}',
                    [
                        LearningController::class,
                        'quizResult',
                    ]
                )
                    ->withoutScopedBindings()
                    ->name('quiz.results');

                Route::post(
                    '/courses/{course:slug}/lessons/{lesson:slug}/complete',
                    [
                        LearningController::class,
                        'completeLesson',
                    ]
                )->name('lessons.complete');
            });
    });

/*
|--------------------------------------------------------------------------
| Public certificate verification
|--------------------------------------------------------------------------
|
| Anyone may verify a certificate without logging in.
| This must remain above the CMS catch-all route.
|
*/

Route::get(
    '/verify/{verificationCode}',
    [
        CertificateController::class,
        'verify',
    ]
)
    ->whereUuid('verificationCode')
    ->name('certificates.verify');

/*
|--------------------------------------------------------------------------
| Legacy verification URL
|--------------------------------------------------------------------------
|
| Previously generated QR codes may still point to this address.
| Keep it so existing certificates remain verifiable.
|
*/

Route::get(
    '/certificates/verify/{verificationCode}',
    function (string $verificationCode) {
        return redirect()->route(
            'certificates.verify',
            [
                'verificationCode' =>
                    $verificationCode,
            ],
            301
        );
    }
)->whereUuid('verificationCode');

Route::middleware('auth')
    ->group(function (): void {
        Route::get(
            '/learn/certificates/{certificate}/view',
            [
                CertificateController::class,
                'stream',
            ]
        )->name('certificates.stream');

        Route::get(
            '/learn/certificates/{certificate}/download',
            [
                CertificateController::class,
                'download',
            ]
        )->name('certificates.download');
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