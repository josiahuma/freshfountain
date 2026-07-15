<?php

namespace App\Services;

use App\Jobs\SendLearningEmail;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\LearningEmailLog;
use App\Models\LessonCompletion;
use App\Models\QuizAttempt;

class LearningEmailService
{
    public function queueEnrolmentWelcome(
        CourseEnrollment $enrollment
    ): void {
        $this->createAndDispatch(
            [
                'user_id' => $enrollment->user_id,

                'course_enrollment_id' =>
                    $enrollment->id,

                'type' =>
                    LearningEmailLog::TYPE_ENROLMENT_WELCOME,

                'dedupe_key' =>
                    "enrollment:{$enrollment->id}:welcome",
            ]
        );
    }

    public function queueLessonCompleted(
        LessonCompletion $completion
    ): void {
        $this->createAndDispatch(
            [
                'user_id' => $completion->user_id,

                'course_enrollment_id' =>
                    $completion->course_enrollment_id,

                'lesson_id' =>
                    $completion->lesson_id,

                'type' =>
                    LearningEmailLog::TYPE_LESSON_COMPLETED,

                'dedupe_key' =>
                    "lesson-completion:{$completion->id}",
            ]
        );
    }

    public function queueQuizPassed(
        QuizAttempt $attempt
    ): void {
        if (! $attempt->passed) {
            return;
        }

        $this->createAndDispatch(
            [
                'user_id' => $attempt->user_id,

                'course_enrollment_id' =>
                    $attempt->course_enrollment_id,

                'quiz_attempt_id' =>
                    $attempt->id,

                'type' =>
                    LearningEmailLog::TYPE_QUIZ_PASSED,

                'dedupe_key' =>
                    "quiz-attempt:{$attempt->id}:passed",
            ]
        );
    }

    public function queueCourseCompleted(
        CourseEnrollment $enrollment
    ): void {
        if (
            $enrollment->status
            !== CourseEnrollment::STATUS_COMPLETED
            || $enrollment->progress_percentage < 100
        ) {
            return;
        }

        $this->createAndDispatch(
            [
                'user_id' => $enrollment->user_id,

                'course_enrollment_id' =>
                    $enrollment->id,

                'type' =>
                    LearningEmailLog::TYPE_COURSE_COMPLETED,

                'dedupe_key' =>
                    "enrollment:{$enrollment->id}:completed",
            ]
        );
    }

    public function queueCertificateReady(
        Certificate $certificate
    ): void {
        $this->createAndDispatch(
            [
                'user_id' =>
                    $certificate->user_id,

                'course_enrollment_id' =>
                    $certificate->course_enrollment_id,

                'certificate_id' =>
                    $certificate->id,

                'type' =>
                    LearningEmailLog::TYPE_CERTIFICATE_READY,

                'dedupe_key' =>
                    "certificate:{$certificate->id}:ready",
            ]
        );
    }

    private function createAndDispatch(
        array $attributes
    ): void {
        $log = LearningEmailLog::query()
            ->firstOrCreate(
                [
                    'dedupe_key' =>
                        $attributes['dedupe_key'],
                ],
                $attributes
            );

        if (! $log->wasRecentlyCreated) {
            return;
        }

        SendLearningEmail::dispatch(
            $log->id
        )->afterCommit();
    }
}