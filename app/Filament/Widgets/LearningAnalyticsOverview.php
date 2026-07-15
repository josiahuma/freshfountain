<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CourseEnrollments\CourseEnrollmentResource;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\QuizAttempt;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class LearningAnalyticsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading =
        'Learning Analytics';

    protected ?string $description =
        'Live enrolment, activity, assessment, completion and certificate statistics.';

    protected ?string $pollingInterval =
        '30s';

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $totalEnrollments =
            CourseEnrollment::query()->count();

        $uniqueStudents =
            CourseEnrollment::query()
                ->distinct()
                ->count('user_id');

        $activeToday =
            CourseEnrollment::query()
                ->whereDate(
                    'last_activity_at',
                    today()
                )
                ->distinct()
                ->count('user_id');

        $completedEnrollments =
            CourseEnrollment::query()
                ->where(
                    'status',
                    CourseEnrollment::STATUS_COMPLETED
                )
                ->count();

        $completionRate =
            $totalEnrollments > 0
                ? round(
                    (
                        $completedEnrollments
                        / $totalEnrollments
                    ) * 100
                )
                : 0;

        $quizAttemptsCount =
            QuizAttempt::query()->count();

        $averageQuizScore =
            QuizAttempt::query()
                ->whereNotNull('percentage')
                ->avg('percentage');

        $validCertificates =
            Certificate::query()
                ->whereNull('revoked_at')
                ->count();

        $revokedCertificates =
            Certificate::query()
                ->whereNotNull('revoked_at')
                ->count();

        $mostDifficultLesson =
            DB::table('quiz_attempts')
                ->join(
                    'quizzes',
                    'quizzes.id',
                    '=',
                    'quiz_attempts.quiz_id'
                )
                ->join(
                    'lessons',
                    'lessons.id',
                    '=',
                    'quizzes.lesson_id'
                )
                ->whereNotNull(
                    'quiz_attempts.percentage'
                )
                ->select([
                    'lessons.id',
                    'lessons.course_id',
                    'lessons.title',

                    DB::raw(
                        'AVG(quiz_attempts.percentage) as average_score'
                    ),

                    DB::raw(
                        'COUNT(quiz_attempts.id) as attempts_count'
                    ),
                ])
                ->groupBy(
                    'lessons.id',
                    'lessons.course_id',
                    'lessons.title'
                )
                ->orderBy('average_score')
                ->first();

        $mostFailedQuiz =
            DB::table('quiz_attempts')
                ->join(
                    'quizzes',
                    'quizzes.id',
                    '=',
                    'quiz_attempts.quiz_id'
                )
                ->join(
                    'lessons',
                    'lessons.id',
                    '=',
                    'quizzes.lesson_id'
                )
                ->where(
                    'quiz_attempts.passed',
                    false
                )
                ->select([
                    'quizzes.id',
                    'quizzes.title',
                    'lessons.course_id',

                    DB::raw(
                        'COUNT(quiz_attempts.id) as failed_count'
                    ),
                ])
                ->groupBy(
                    'quizzes.id',
                    'quizzes.title',
                    'lessons.course_id'
                )
                ->orderByDesc('failed_count')
                ->first();

        $studentProgressUrl =
            CourseEnrollmentResource::getUrl(
                'index'
            );

        $activeTodayUrl =
            CourseEnrollmentResource::getUrl(
                'index',
                [
                    'tableFilters' => [
                        'active_today' => [
                            'isActive' => true,
                        ],
                    ],
                ]
            );

        $quizAttemptsUrl =
            CourseEnrollmentResource::getUrl(
                'index',
                [
                    'tableFilters' => [
                        'has_quiz_attempts' => [
                            'isActive' => true,
                        ],
                    ],
                ]
            );

        $completedUrl =
            CourseEnrollmentResource::getUrl(
                'index',
                [
                    'activeTab' =>
                        'completed',
                ]
            );

        $difficultLessonUrl =
            $mostDifficultLesson
                ? CourseEnrollmentResource::getUrl(
                    'index',
                    [
                        'tableFilters' => [
                            'course_id' => [
                                'value' =>
                                    $mostDifficultLesson
                                        ->course_id,
                            ],

                            'has_quiz_attempts' => [
                                'isActive' => true,
                            ],
                        ],
                    ]
                )
                : $studentProgressUrl;

        $failedQuizUrl =
            $mostFailedQuiz
                ? CourseEnrollmentResource::getUrl(
                    'index',
                    [
                        'tableFilters' => [
                            'course_id' => [
                                'value' =>
                                    $mostFailedQuiz
                                        ->course_id,
                            ],

                            'has_failed_quiz_attempts' => [
                                'isActive' => true,
                            ],
                        ],
                    ]
                )
                : $studentProgressUrl;

        return [
            Stat::make(
                'Students enrolled',
                number_format($uniqueStudents)
            )
                ->description(
                    number_format(
                        $totalEnrollments
                    )
                    . ' total course enrolments'
                )
                ->descriptionIcon(
                    'heroicon-m-academic-cap'
                )
                ->color('info')
                ->url($studentProgressUrl),

            Stat::make(
                'Active today',
                number_format($activeToday)
            )
                ->description(
                    $activeToday === 1
                        ? '1 learner active today'
                        : "{$activeToday} learners active today"
                )
                ->descriptionIcon(
                    'heroicon-m-bolt'
                )
                ->color(
                    $activeToday > 0
                        ? 'success'
                        : 'gray'
                )
                ->url($activeTodayUrl),

            Stat::make(
                'Average quiz score',
                $averageQuizScore === null
                    ? 'No attempts'
                    : round(
                        (float) $averageQuizScore
                    ) . '%'
            )
                ->description(
                    number_format(
                        $quizAttemptsCount
                    )
                    . ' recorded quiz attempts'
                )
                ->descriptionIcon(
                    'heroicon-m-question-mark-circle'
                )
                ->color('warning')
                ->url($quizAttemptsUrl),

            Stat::make(
                'Completion rate',
                $completionRate . '%'
            )
                ->description(
                    "{$completedEnrollments} of {$totalEnrollments} enrolments completed"
                )
                ->descriptionIcon(
                    'heroicon-m-check-badge'
                )
                ->color(
                    match (true) {
                        $completionRate >= 70 =>
                            'success',

                        $completionRate >= 30 =>
                            'warning',

                        default =>
                            'gray',
                    }
                )
                ->url($completedUrl),

            Stat::make(
                'Most difficult lesson',
                $mostDifficultLesson?->title
                    ?? 'No quiz data'
            )
                ->description(
                    $mostDifficultLesson
                        ? round(
                            (float)
                            $mostDifficultLesson
                                ->average_score
                        )
                            . '% average from '
                            . $mostDifficultLesson
                                ->attempts_count
                            . ' attempts'
                        : 'Waiting for student quiz attempts'
                )
                ->descriptionIcon(
                    'heroicon-m-book-open'
                )
                ->color(
                    $mostDifficultLesson
                        ? 'danger'
                        : 'gray'
                )
                ->url($difficultLessonUrl),

            Stat::make(
                'Most failed quiz',
                $mostFailedQuiz?->title
                    ?? 'No failed quizzes'
            )
                ->description(
                    $mostFailedQuiz
                        ? $mostFailedQuiz
                            ->failed_count
                            . ' failed '
                            . (
                                (int)
                                $mostFailedQuiz
                                    ->failed_count
                                === 1
                                    ? 'attempt'
                                    : 'attempts'
                            )
                        : 'No quiz failures recorded'
                )
                ->descriptionIcon(
                    'heroicon-m-exclamation-triangle'
                )
                ->color(
                    $mostFailedQuiz
                        ? 'danger'
                        : 'success'
                )
                ->url($failedQuizUrl),

            Stat::make(
                'Certificates issued',
                number_format(
                    $validCertificates
                )
            )
                ->description(
                    $revokedCertificates
                    . ' revoked'
                )
                ->descriptionIcon(
                    'heroicon-m-trophy'
                )
                ->color('success')
                ->url($completedUrl),
        ];
    }
}