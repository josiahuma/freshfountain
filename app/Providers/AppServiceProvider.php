<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\CourseEnrollment;
use App\Observers\CourseEnrollmentObserver;
use App\Models\LessonCompletion;
use App\Models\QuizAttempt;
use App\Observers\LessonCompletionObserver;
use App\Observers\QuizAttemptObserver;
use Stripe\Stripe;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Stripe::setEnableTelemetry(false);

        Schema::defaultStringLength(191);
        CourseEnrollment::observe(
            CourseEnrollmentObserver::class
        );

        LessonCompletion::observe(
            LessonCompletionObserver::class
        );

        QuizAttempt::observe(
            QuizAttemptObserver::class
        );
    }
}
