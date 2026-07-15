<?php

namespace App\Jobs;

use App\Models\LearningEmailLog;
use App\Services\MicrosoftGraphMailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SendLearningEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [
        60,
        300,
        900,
    ];

    public function __construct(
        public int $learningEmailLogId
    ) {
    }

    public function handle(
        MicrosoftGraphMailer $mailer
    ): void {
        $log = LearningEmailLog::query()
            ->with([
                'user',
                'enrollment.course',
                'lesson.course',
                'quizAttempt.quiz.lesson.course',
                'certificate.course',
            ])
            ->findOrFail(
                $this->learningEmailLogId
            );

        if ($log->sent_at) {
            return;
        }

        $email = $log->user?->email;

        if (blank($email)) {
            $log->update([
                'failed_at' => now(),
                'error_message' =>
                    'The learner does not have an email address.',
            ]);

            return;
        }

        $message = $this->buildMessage(
            $log
        );

        $log->increment('attempts');

        $html = view(
            'emails.learning.notification',
            [
                'recipientName' =>
                    $log->user->name,

                'eyebrow' =>
                    $message['eyebrow'],

                'heading' =>
                    $message['heading'],

                'intro' =>
                    $message['intro'],

                'details' =>
                    $message['details'],

                'actionLabel' =>
                    $message['action_label'],

                'actionUrl' =>
                    $message['action_url'],

                'closing' =>
                    $message['closing'],
            ]
        )->render();

        $mailer->send(
            $email,
            $message['subject'],
            $html
        );

        $log->update([
            'sent_at' => now(),
            'failed_at' => null,
            'error_message' => null,
        ]);
    }

    public function failed(
        ?Throwable $exception
    ): void {
        LearningEmailLog::query()
            ->whereKey(
                $this->learningEmailLogId
            )
            ->update([
                'failed_at' => now(),

                'error_message' =>
                    $exception?->getMessage()
                    ?? 'The email job failed.',
            ]);
    }

    private function buildMessage(
        LearningEmailLog $log
    ): array {
        return match ($log->type) {
            LearningEmailLog::TYPE_ENROLMENT_WELCOME =>
                $this->enrolmentWelcomeMessage(
                    $log
                ),

            LearningEmailLog::TYPE_LESSON_COMPLETED =>
                $this->lessonCompletedMessage(
                    $log
                ),

            LearningEmailLog::TYPE_QUIZ_PASSED =>
                $this->quizPassedMessage(
                    $log
                ),

            LearningEmailLog::TYPE_COURSE_COMPLETED =>
                $this->courseCompletedMessage(
                    $log
                ),

            LearningEmailLog::TYPE_CERTIFICATE_READY =>
                $this->certificateReadyMessage(
                    $log
                ),

            default => throw new \RuntimeException(
                "Unknown learning email type: {$log->type}"
            ),
        };
    }

    private function enrolmentWelcomeMessage(
        LearningEmailLog $log
    ): array {
        $enrollment = $log->enrollment;
        $course = $enrollment->course;

        return [
            'subject' =>
                "Welcome to {$course->title}",

            'eyebrow' =>
                'Fresh Learning',

            'heading' =>
                "Welcome to {$course->title}",

            'intro' =>
                'Your enrolment is confirmed and your course is ready. You can begin learning immediately and your progress will be saved automatically.',

            'details' => [
                'Course' =>
                    $course->title,

                'Enrolled' =>
                    optional(
                        $enrollment->enrolled_at
                    )?->format('j F Y')
                    ?? now()->format('j F Y'),

                'Status' =>
                    'Active',
            ],

            'action_label' =>
                'Start course',

            'action_url' =>
                route(
                    'learn.courses.show',
                    $course
                ),

            'closing' =>
                'We pray that this course strengthens your faith and helps you grow.',
        ];
    }

    private function lessonCompletedMessage(
        LearningEmailLog $log
    ): array {
        $lesson = $log->lesson;
        $course = $lesson->course;
        $enrollment = $log->enrollment;

        return [
            'subject' =>
                "Lesson completed – {$lesson->title}",

            'eyebrow' =>
                'Lesson completed',

            'heading' =>
                'Well done!',

            'intro' =>
                "You have successfully completed “{$lesson->title}”. Your course progress has been updated.",

            'details' => [
                'Course' =>
                    $course->title,

                'Lesson' =>
                    $lesson->title,

                'Course progress' =>
                    "{$enrollment->progress_percentage}%",
            ],

            'action_label' =>
                'Continue learning',

            'action_url' =>
                route(
                    'learn.courses.show',
                    $course
                ),

            'closing' =>
                'Keep going—you are making excellent progress.',
        ];
    }

    private function quizPassedMessage(
        LearningEmailLog $log
    ): array {
        $attempt = $log->quizAttempt;
        $quiz = $attempt->quiz;
        $lesson = $quiz->lesson;
        $course = $lesson->course;

        return [
            'subject' =>
                "Quiz passed – {$quiz->title}",

            'eyebrow' =>
                'Quiz passed',

            'heading' =>
                'Congratulations!',

            'intro' =>
                "You passed “{$quiz->title}” and may continue with your course.",

            'details' => [
                'Course' =>
                    $course->title,

                'Quiz' =>
                    $quiz->title,

                'Your score' =>
                    "{$attempt->percentage}%",

                'Required pass mark' =>
                    "{$quiz->pass_percentage}%",

                'Attempt' =>
                    "#{$attempt->attempt_number}",
            ],

            'action_label' =>
                'View your result',

            'action_url' =>
                route(
                    'learn.quiz.results',
                    [
                        'course' =>
                            $course,

                        'lesson' =>
                            $lesson,

                        'attempt' =>
                            $attempt,
                    ]
                ),

            'closing' =>
                'Excellent work. Continue to the next stage of your learning.',
        ];
    }

    private function courseCompletedMessage(
        LearningEmailLog $log
    ): array {
        $enrollment = $log->enrollment;
        $course = $enrollment->course;

        return [
            'subject' =>
                "Congratulations on completing {$course->title}",

            'eyebrow' =>
                'Course completed',

            'heading' =>
                'Congratulations!',

            'intro' =>
                "You have completed every required lesson and assessment in {$course->title}.",

            'details' => [
                'Course' =>
                    $course->title,

                'Progress' =>
                    '100%',

                'Completed' =>
                    optional(
                        $enrollment->completed_at
                    )?->format('j F Y')
                    ?? now()->format('j F Y'),
            ],

            'action_label' =>
                'View completed course',

            'action_url' =>
                route(
                    'learn.courses.show',
                    $course
                ),

            'closing' =>
                'Thank you for your commitment to learning and spiritual growth.',
        ];
    }

    private function certificateReadyMessage(
        LearningEmailLog $log
    ): array {
        $certificate = $log->certificate;

        return [
            'subject' =>
                "Your {$certificate->course_title} certificate is ready",

            'eyebrow' =>
                'Certificate ready',

            'heading' =>
                'Your certificate is ready',

            'intro' =>
                "Your official certificate for {$certificate->course_title} has been generated and is ready to download.",

            'details' => [
                'Course' =>
                    $certificate->course_title,

                'Certificate number' =>
                    $certificate->certificate_number,

                'Issued' =>
                    $certificate
                        ->issued_at
                        ->format('j F Y'),

                'Verification code' =>
                    $certificate
                        ->verification_code,
            ],

            'action_label' =>
                'Download certificate',

            'action_url' =>
                route(
                    'certificates.download',
                    $certificate
                ),

            'closing' =>
                'Your certificate can also be independently verified using its QR code or verification address.',
        ];
    }
}