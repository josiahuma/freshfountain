<?php

namespace App\Observers;

use App\Models\QuizAttempt;
use App\Services\LearningEmailService;
use Throwable;

class QuizAttemptObserver
{
    public function __construct(
        private readonly LearningEmailService $learningEmailService
    ) {
    }

    public function created(
        QuizAttempt $attempt
    ): void {
        if ($attempt->passed) {
            $this->queuePassedEmail(
                $attempt
            );
        }
    }

    public function updated(
        QuizAttempt $attempt
    ): void {
        if (
            ! $attempt->wasChanged('passed')
            || ! $attempt->passed
        ) {
            return;
        }

        $this->queuePassedEmail(
            $attempt
        );
    }

    private function queuePassedEmail(
        QuizAttempt $attempt
    ): void {
        try {
            $this->learningEmailService
                ->queueQuizPassed(
                    $attempt
                );
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}