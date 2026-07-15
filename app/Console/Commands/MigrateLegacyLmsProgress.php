<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class MigrateLegacyLmsProgress extends Command
{
    protected $signature = 'legacy-lms:migrate-progress
        {--dry-run : Analyse and report without writing anything}
        {--skip-quizzes : Do not import historical quiz attempts}';

    protected $description =
        'Migrate legacy LMS students, enrolments, lesson completions and quiz attempts into Fresh Learning.';

    private bool $dryRun = false;

    private array $stats = [
        'students_seen' => 0,
        'students_created' => 0,
        'students_matched' => 0,
        'enrollments_created' => 0,
        'enrollments_matched' => 0,
        'completions_created' => 0,
        'completions_matched' => 0,
        'quiz_attempts_created' => 0,
        'quiz_attempts_matched' => 0,
        'skipped_marriage_enrollments' => 0,
        'skipped_records' => 0,
    ];

    private array $userMap = [];

    private array $courseMap = [];

    private array $lessonMap = [];

    private array $quizMap = [];

    public function handle(): int
    {
        $this->dryRun = (bool) $this->option('dry-run');

        $this->newLine();
        $this->info(
            $this->dryRun
                ? 'Legacy LMS migration DRY RUN'
                : 'Legacy LMS migration'
        );

        if (! $this->checkLegacyTables()) {
            return self::FAILURE;
        }

        try {
            $this->buildCourseMap();
            $this->buildLessonMap();
            $this->buildQuizMap();

            $this->migrateStudents();
            $this->migrateEnrollments();
            $this->migrateLessonCompletions();

            if (! $this->option('skip-quizzes')) {
                $this->migrateQuizAttempts();
            }

            if (! $this->dryRun) {
                $this->recalculateAffectedEnrollments();
            }

            $this->displaySummary();

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);

            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    private function checkLegacyTables(): bool
    {
        $required = [
            'users',
            'courses',
            'lessons',
            'quizzes',
            'enrollments',
            'lesson_completions',
            'quiz_submissions',
        ];

        foreach ($required as $table) {
            if (
                ! DB::connection('legacy_lms')
                    ->getSchemaBuilder()
                    ->hasTable($table)
            ) {
                $this->error(
                    "Legacy table missing: {$table}"
                );

                return false;
            }
        }

        return true;
    }

    private function buildCourseMap(): void
    {
        $legacyCourses = DB::connection('legacy_lms')
            ->table('courses')
            ->get();

        foreach ($legacyCourses as $legacyCourse) {
            $legacyTitle = $this->normalise(
                $legacyCourse->title
            );

            $newCourse = match ($legacyTitle) {
                'membership class' =>
                    Course::query()
                        ->where('slug', 'membership-class')
                        ->first(),

                'baptism class' =>
                    Course::query()
                        ->where('slug', 'baptismal-class')
                        ->first(),

                default => null,
            };

            if ($newCourse) {
                $this->courseMap[
                    (int) $legacyCourse->id
                ] = $newCourse->id;

                $this->line(
                    "Course mapped: {$legacyCourse->title} → {$newCourse->title}"
                );
            } else {
                $this->warn(
                    "Course skipped: {$legacyCourse->title}"
                );
            }
        }
    }

    private function buildLessonMap(): void
    {
        $legacyLessons = DB::connection('legacy_lms')
            ->table('lessons')
            ->get();

        foreach ($legacyLessons as $legacyLesson) {
            $newCourseId =
                $this->courseMap[
                    (int) $legacyLesson->course_id
                ] ?? null;

            if (! $newCourseId) {
                continue;
            }

            $legacyTitle = $this->normalise(
                $legacyLesson->title
            );

            $newLesson = Lesson::query()
                ->where('course_id', $newCourseId)
                ->get()
                ->first(
                    fn (Lesson $lesson): bool =>
                        $this->normalise($lesson->title)
                        === $legacyTitle
                );

            /*
             * The old baptism lesson was named
             * "Fresh Fountain Baptism Course", while the
             * new lesson is named "Baptismal Class".
             */
            if (
                ! $newLesson
                && $legacyTitle
                    === 'fresh fountain baptism course'
            ) {
                $newLesson = Lesson::query()
                    ->where('course_id', $newCourseId)
                    ->where('slug', 'baptismal-class')
                    ->first();
            }

            if (! $newLesson) {
                $this->warn(
                    "Lesson not mapped: {$legacyLesson->title}"
                );

                $this->stats['skipped_records']++;

                continue;
            }

            $this->lessonMap[
                (int) $legacyLesson->id
            ] = $newLesson->id;
        }

        $this->info(
            count($this->lessonMap)
            . ' legacy lessons mapped.'
        );
    }

    private function buildQuizMap(): void
    {
        $legacyQuizzes = DB::connection('legacy_lms')
            ->table('quizzes')
            ->get();

        foreach ($legacyQuizzes as $legacyQuiz) {
            $newLessonId =
                $this->lessonMap[
                    (int) $legacyQuiz->lesson_id
                ] ?? null;

            if (! $newLessonId) {
                continue;
            }

            $newQuiz = Quiz::query()
                ->where('lesson_id', $newLessonId)
                ->first();

            if (! $newQuiz) {
                $this->warn(
                    "Quiz not mapped for new lesson ID {$newLessonId}"
                );

                continue;
            }

            $this->quizMap[
                (int) $legacyQuiz->id
            ] = $newQuiz->id;
        }

        $this->info(
            count($this->quizMap)
            . ' legacy quizzes mapped.'
        );
    }

    private function migrateStudents(): void
    {
        $legacyStudents = DB::connection('legacy_lms')
            ->table('users')
            ->where('role', 'student')
            ->orderBy('id')
            ->get();

        foreach ($legacyStudents as $legacyStudent) {
            $this->stats['students_seen']++;

            $email = Str::lower(
                trim((string) $legacyStudent->email)
            );

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->warn(
                    "Invalid student email skipped: {$email}"
                );

                $this->stats['skipped_records']++;

                continue;
            }

            $user = User::query()
                ->whereRaw(
                    'LOWER(email) = ?',
                    [$email]
                )
                ->first();

            if ($user) {
                $this->stats['students_matched']++;
                $this->userMap[
                    (int) $legacyStudent->id
                ] = $user->id;

                continue;
            }

            if ($this->dryRun) {
                $this->stats['students_created']++;
                $this->userMap[
                    (int) $legacyStudent->id
                ] = -1 * (int) $legacyStudent->id;

                continue;
            }

            $user = User::query()->create([
                'name' =>
                    trim((string) $legacyStudent->name),

                'email' => $email,

                /*
                 * Existing Laravel bcrypt hashes are retained.
                 * If a legacy value is unexpectedly invalid,
                 * create a random inaccessible password.
                 */
                'password' =>
                    Str::startsWith(
                        (string) $legacyStudent->password,
                        '$2y$'
                    )
                        ? $legacyStudent->password
                        : Hash::make(Str::random(64)),

                'email_verified_at' =>
                    $legacyStudent->email_verified_at,

                'is_admin' => false,
            ]);

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'created_at' =>
                        $legacyStudent->created_at
                        ?? now(),

                    'updated_at' =>
                        $legacyStudent->updated_at
                        ?? $legacyStudent->created_at
                        ?? now(),
                ]);

            $this->stats['students_created']++;
            $this->userMap[
                (int) $legacyStudent->id
            ] = $user->id;
        }
    }

    private function migrateEnrollments(): void
    {
        $legacyEnrollments =
            DB::connection('legacy_lms')
                ->table('enrollments')
                ->orderBy('id')
                ->get();

        foreach ($legacyEnrollments as $legacyEnrollment) {
            $newUserId =
                $this->userMap[
                    (int) $legacyEnrollment->user_id
                ] ?? null;

            $newCourseId =
                $this->courseMap[
                    (int) $legacyEnrollment->course_id
                ] ?? null;

            if (! $newCourseId) {
                $this->stats[
                    'skipped_marriage_enrollments'
                ]++;

                continue;
            }

            if (! $newUserId) {
                $this->stats['skipped_records']++;

                continue;
            }

            if ($this->dryRun) {
                $exists = $newUserId > 0
                    && DB::table('course_enrollments')
                        ->where('user_id', $newUserId)
                        ->where('course_id', $newCourseId)
                        ->exists();

                $this->stats[
                    $exists
                        ? 'enrollments_matched'
                        : 'enrollments_created'
                ]++;

                continue;
            }

            $exists = DB::table('course_enrollments')
                ->where('user_id', $newUserId)
                ->where('course_id', $newCourseId)
                ->exists();

            if ($exists) {
                $this->stats['enrollments_matched']++;

                continue;
            }

            CourseEnrollment::withoutEvents(
                function () use (
                    $legacyEnrollment,
                    $newUserId,
                    $newCourseId
                ): void {
                    DB::table('course_enrollments')
                        ->insert([
                            'user_id' => $newUserId,
                            'course_id' => $newCourseId,
                            'status' =>
                                CourseEnrollment::STATUS_ACTIVE,
                            'progress_percentage' => 0,
                            'enrolled_at' =>
                                $legacyEnrollment->created_at
                                ?? now(),
                            'started_at' => null,
                            'completed_at' => null,
                            'last_activity_at' =>
                                $legacyEnrollment->updated_at
                                ?? $legacyEnrollment->created_at
                                ?? now(),
                            'last_lesson_id' => null,
                            'pause_reason' => null,
                            'paused_at' => null,
                            'paused_by' => null,
                            'created_at' =>
                                $legacyEnrollment->created_at
                                ?? now(),
                            'updated_at' =>
                                $legacyEnrollment->updated_at
                                ?? $legacyEnrollment->created_at
                                ?? now(),
                        ]);
                }
            );

            $this->stats['enrollments_created']++;
        }
    }

    private function migrateLessonCompletions(): void
    {
        $legacyCompletions =
            DB::connection('legacy_lms')
                ->table('lesson_completions')
                ->orderBy('id')
                ->get();

        foreach ($legacyCompletions as $legacyCompletion) {
            $newUserId =
                $this->userMap[
                    (int) $legacyCompletion->user_id
                ] ?? null;

            $newLessonId =
                $this->lessonMap[
                    (int) $legacyCompletion->lesson_id
                ] ?? null;

            if (! $newUserId || ! $newLessonId) {
                $this->stats['skipped_records']++;

                continue;
            }

            $newCourseId = Lesson::query()
                ->whereKey($newLessonId)
                ->value('course_id');

            if (! $newCourseId) {
                $this->stats['skipped_records']++;

                continue;
            }

            if ($this->dryRun) {
                if ($newUserId < 0) {
                    $this->stats['completions_created']++;

                    continue;
                }

                $exists = DB::table('lesson_completions')
                    ->where('user_id', $newUserId)
                    ->where('lesson_id', $newLessonId)
                    ->exists();

                $this->stats[
                    $exists
                        ? 'completions_matched'
                        : 'completions_created'
                ]++;

                continue;
            }

            $enrollmentId = DB::table(
                'course_enrollments'
            )
                ->where('user_id', $newUserId)
                ->where('course_id', $newCourseId)
                ->value('id');

            if (! $enrollmentId) {
                $this->stats['skipped_records']++;

                continue;
            }

            $exists = DB::table('lesson_completions')
                ->where('user_id', $newUserId)
                ->where('lesson_id', $newLessonId)
                ->exists();

            if ($exists) {
                $this->stats['completions_matched']++;

                continue;
            }

            DB::table('lesson_completions')
                ->insert([
                    'user_id' => $newUserId,
                    'lesson_id' => $newLessonId,
                    'course_enrollment_id' =>
                        $enrollmentId,
                    'started_at' =>
                        $legacyCompletion->created_at
                        ?? now(),
                    'completed_at' =>
                        $legacyCompletion->created_at
                        ?? now(),
                    'created_at' =>
                        $legacyCompletion->created_at
                        ?? now(),
                    'updated_at' =>
                        $legacyCompletion->updated_at
                        ?? $legacyCompletion->created_at
                        ?? now(),
                ]);

            $this->stats['completions_created']++;
        }
    }

    private function migrateQuizAttempts(): void
    {
        $legacySubmissions =
            DB::connection('legacy_lms')
                ->table('quiz_submissions')
                ->orderBy('user_id')
                ->orderBy('quiz_id')
                ->orderBy('created_at')
                ->orderBy('id')
                ->get();

        $attemptCounters = [];

        foreach ($legacySubmissions as $legacySubmission) {
            $newUserId =
                $this->userMap[
                    (int) $legacySubmission->user_id
                ] ?? null;

            $newQuizId =
                $this->quizMap[
                    (int) $legacySubmission->quiz_id
                ] ?? null;

            if (! $newUserId || ! $newQuizId) {
                $this->stats['skipped_records']++;

                continue;
            }

            if (
                ! $this->dryRun
                && DB::table('quiz_attempts')
                    ->where(
                        'legacy_source',
                        'freshfountain_coursesdb'
                    )
                    ->where(
                        'legacy_id',
                        $legacySubmission->id
                    )
                    ->exists()
            ) {
                $this->stats[
                    'quiz_attempts_matched'
                ]++;

                continue;
            }

            $quiz = Quiz::query()
                ->with('lesson')
                ->find($newQuizId);

            if (! $quiz) {
                $this->stats['skipped_records']++;

                continue;
            }

            $newCourseId = $quiz->lesson->course_id;

            if ($this->dryRun && $newUserId < 0) {
                $this->stats[
                    'quiz_attempts_created'
                ]++;

                continue;
            }

            $enrollmentId = $this->dryRun
                ? null
                : DB::table('course_enrollments')
                    ->where('user_id', $newUserId)
                    ->where('course_id', $newCourseId)
                    ->value('id');

            if (! $this->dryRun && ! $enrollmentId) {
                $this->stats['skipped_records']++;

                continue;
            }

            $counterKey =
                "{$newUserId}:{$newQuizId}";

            /*
            * Existing learners may already have attempts in the
            * new LMS. Begin imported historical numbering after
            * their current highest attempt number.
            */
            if (! array_key_exists(
                $counterKey,
                $attemptCounters
            )) {
                $attemptCounters[$counterKey] =
                    $newUserId > 0
                        ? (int) DB::table('quiz_attempts')
                            ->where(
                                'user_id',
                                $newUserId
                            )
                            ->where(
                                'quiz_id',
                                $newQuizId
                            )
                            ->max('attempt_number')
                        : 0;
            }

            $attemptCounters[$counterKey]++;

            if ($this->dryRun) {
                $this->stats[
                    'quiz_attempts_created'
                ]++;

                continue;
            }

            $maximumPoints = max(
                1,
                (int) $quiz
                    ->publishedQuestions()
                    ->sum('points')
            );

            $percentage = max(
                0,
                min(
                    100,
                    (int) $legacySubmission->score
                )
            );

            $scorePoints = (int) round(
                ($percentage / 100)
                * $maximumPoints
            );

            DB::table('quiz_attempts')
                ->insert([
                    'legacy_source' =>
                        'freshfountain_coursesdb',
                    'legacy_id' =>
                        $legacySubmission->id,
                    'quiz_id' => $newQuizId,
                    'user_id' => $newUserId,
                    'course_enrollment_id' =>
                        $enrollmentId,
                    'attempt_number' =>
                        $attemptCounters[$counterKey],
                    'score_points' => $scorePoints,
                    'maximum_points' => $maximumPoints,
                    'percentage' => $percentage,
                    'passed' =>
                        $percentage
                        >= $quiz->pass_percentage,
                    'started_at' =>
                        $legacySubmission->created_at
                        ?? now(),
                    'submitted_at' =>
                        $legacySubmission->created_at
                        ?? now(),
                    'created_at' =>
                        $legacySubmission->created_at
                        ?? now(),
                    'updated_at' =>
                        $legacySubmission->updated_at
                        ?? $legacySubmission->created_at
                        ?? now(),
                ]);

            $this->stats[
                'quiz_attempts_created'
            ]++;
        }
    }

    private function recalculateAffectedEnrollments(): void
    {
        $userIds = array_values(
            array_filter(
                $this->userMap,
                fn (int $id): bool => $id > 0
            )
        );

        $enrollments = CourseEnrollment::query()
            ->with([
                'course',
                'lessonCompletions',
            ])
            ->whereIn('user_id', $userIds)
            ->get();

        $this->info(
            'Recalculating '
            . $enrollments->count()
            . ' enrolments without observers...'
        );

        CourseEnrollment::withoutEvents(
            function () use ($enrollments): void {
                foreach ($enrollments as $enrollment) {
                    $lessonCount = $enrollment
                        ->course
                        ->publishedLessons()
                        ->count();

                    $completedCount = $enrollment
                        ->lessonCompletions()
                        ->whereHas(
                            'lesson',
                            fn ($query) =>
                                $query
                                    ->where(
                                        'course_id',
                                        $enrollment->course_id
                                    )
                                    ->where(
                                        'is_published',
                                        true
                                    )
                        )
                        ->count();

                    $percentage = $lessonCount > 0
                        ? min(
                            100,
                            (int) round(
                                ($completedCount / $lessonCount)
                                * 100
                            )
                        )
                        : 0;

                    $latestCompletion =
                        $enrollment
                            ->lessonCompletions()
                            ->latest('completed_at')
                            ->first();

                    $data = [
                        'progress_percentage' =>
                            $percentage,

                        'last_activity_at' =>
                            $latestCompletion?->completed_at
                            ?? $enrollment->last_activity_at
                            ?? $enrollment->enrolled_at
                            ?? now(),

                        'started_at' =>
                            $completedCount > 0
                                ? (
                                    $enrollment->started_at
                                    ?? $enrollment
                                        ->lessonCompletions()
                                        ->oldest('completed_at')
                                        ->value('completed_at')
                                )
                                : $enrollment->started_at,

                        'last_lesson_id' =>
                            $latestCompletion?->lesson_id
                            ?? $enrollment->last_lesson_id,
                    ];

                    if ($percentage >= 100) {
                        $data['status'] =
                            CourseEnrollment::STATUS_COMPLETED;

                        $data['completed_at'] =
                            $latestCompletion?->completed_at
                            ?? now();
                    } else {
                        $data['status'] =
                            CourseEnrollment::STATUS_ACTIVE;

                        $data['completed_at'] = null;
                    }

                    DB::table('course_enrollments')
                        ->where('id', $enrollment->id)
                        ->update([
                            ...$data,
                            'updated_at' => now(),
                        ]);
                }
            }
        );
    }

    private function displaySummary(): void
    {
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            collect($this->stats)
                ->map(
                    fn (int $count, string $metric): array => [
                        Str::headline($metric),
                        $count,
                    ]
                )
                ->values()
                ->all()
        );

        if ($this->dryRun) {
            $this->warn(
                'Dry run complete. Nothing was written.'
            );
        } else {
            $this->info(
                'Legacy progress migration complete.'
            );

            $this->warn(
                'No historical emails or certificates were generated.'
            );
        }
    }

    private function normalise(?string $value): string
    {
        return Str::of((string) $value)
            ->lower()
            ->replace(['–', '—'], '-')
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();
    }
}