<?php

use App\Http\Controllers\CareerController;
use App\Http\Controllers\Admin\JobApplicationPdfController;
use App\Models\BlogPost;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

function pageViewFor(Page $page): string
{
    return match ($page->template) {
        'home'    => 'pages.home',
        'service' => 'pages.service',
        'services_index' => 'pages.services_index',
        'about'   => 'pages.about',
        'leaders' => 'pages.leaders',
        'contact' => 'pages.contact',
        'jobs'    => 'pages.jobs',
        'blog'    => 'pages.blog',
        'course'  => 'pages.course',
        'courses_index' => 'pages.courses_index',
        'units'   => 'pages.units',
        'units_index' => 'pages.units_index',
        default   => 'pages.service',
    };
}

Route::get('/', function () {
    $page = Page::where('slug', 'home')->where('is_published', true)->firstOrFail();
    return view(pageViewFor($page), compact('page'));
});

//
// ✅ Careers / Job Portal (MUST be above catch-all)
//
Route::get('/careers', [CareerController::class, 'index'])->name('careers.index');
Route::get('/careers/{slug}', [CareerController::class, 'show'])->name('careers.show');
Route::get('/careers/{slug}/apply', [CareerController::class, 'apply'])->name('careers.apply');
Route::post('/careers/{slug}/apply', [CareerController::class, 'submit'])->name('careers.submit');
Route::get('/careers/{slug}/success', [CareerController::class, 'success'])->name('careers.success');

//
// ✅ PDF export for a single application (MUST be above catch-all)
//
Route::get('/admin/job-applications/{jobApplication}/pdf', [JobApplicationPdfController::class, 'show'])
    ->name('admin.job-applications.pdf');

//
// ✅ Single blog post (MUST be above catch-all)
//
Route::get('/blog/{slug}', function (string $slug) {
    $post = BlogPost::published()->where('slug', $slug)->firstOrFail();
    return view('blog.show', compact('post'));
})->where('slug', '[A-Za-z0-9\-]+');

//
// Catch-all CMS pages
//
Route::get('{slug}', function (string $slug) {
    $page = Page::where('slug', $slug)->where('is_published', true)->firstOrFail();
    return view(pageViewFor($page), compact('page'));
})->where('slug', '^(?!admin|_debug-upload-limits|careers).*');

//
// Debug route
//
Route::get('/_debug-upload-limits', function () {
    return response()->json([
        'post_max_size' => ini_get('post_max_size'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'memory_limit' => ini_get('memory_limit'),
    ]);
});
