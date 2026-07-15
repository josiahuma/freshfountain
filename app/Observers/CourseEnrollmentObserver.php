<?php

namespace App\Observers;

use App\Models\CourseEnrollment;
use App\Services\CertificateService;
use App\Services\LearningEmailService;
use Throwable;

class CourseEnrollmentObserver
{
    public function __construct(
        private readonly CertificateService $certificateService,

        private readonly LearningEmailService $learningEmailService
    ) {
    }

    public function created(
        CourseEnrollment $enrollment
    ): void {
        try {
            $this->learningEmailService
                ->queueEnrolmentWelcome(
                    $enrollment
                );
        } catch (Throwable $exception) {
            report($exception);
        }

        $this->handleCompletedEnrollment(
            $enrollment
        );
    }

    public function updated(
        CourseEnrollment $enrollment
    ): void {
        if (
            ! $enrollment->wasChanged([
                'status',
                'progress_percentage',
                'completed_at',
            ])
        ) {
            return;
        }

        $this->handleCompletedEnrollment(
            $enrollment
        );
    }

    private function handleCompletedEnrollment(
        CourseEnrollment $enrollment
    ): void {
        if (
            $enrollment->status
            !== CourseEnrollment::STATUS_COMPLETED
            || $enrollment->progress_percentage < 100
        ) {
            return;
        }

        try {
            $this->learningEmailService
                ->queueCourseCompleted(
                    $enrollment
                );
        } catch (Throwable $exception) {
            report($exception);
        }

        try {
            $this->certificateService
                ->issueForEnrollment(
                    $enrollment
                );
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}