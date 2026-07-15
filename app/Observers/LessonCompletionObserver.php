<?php

namespace App\Observers;

use App\Models\LessonCompletion;
use App\Services\LearningEmailService;
use Throwable;

class LessonCompletionObserver
{
    public function __construct(
        private readonly LearningEmailService $learningEmailService
    ) {
    }

    public function created(
        LessonCompletion $completion
    ): void {
        try {
            $this->learningEmailService
                ->queueLessonCompleted(
                    $completion
                );
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}