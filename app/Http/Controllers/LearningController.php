<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizQuestion;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LearningController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();

        $enrollments = CourseEnrollment::query()
            ->with([
                'course' => fn ($query) => $query
                    ->withCount('publishedLessons'),
                'lastLesson',
                'certificate',
            ])
            ->where('user_id', $user->id)
            ->latest('last_activity_at')
            ->get();

        $availableCourses = Course::query()
            ->published()
            ->ordered()
            ->withCount('publishedLessons')
            ->whereDoesntHave(
                'enrollments',
                fn (Builder $query): Builder =>
                    $query->where(
                        'user_id',
                        $user->id
                    )
            )
            ->take(6)
            ->get();

        return view('learn.dashboard', [
            'user' => $user,
            'enrollments' => $enrollments,
            'availableCourses' => $availableCourses,
        ]);
    }

    public function course(Course $course): View
    {
        $this->ensureCourseIsAvailable($course);

        $user = Auth::user();

        $course->load([
            'publishedLessons.quiz',
        ]);

        $enrollment = CourseEnrollment::query()
            ->with([
                'lessonCompletions',
                'lastLesson',
                'certificate',
            ])
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        if ($enrollment) {
            /*
             * Recalculate when the page opens as an additional
             * safeguard against stale historical progress.
             */
            $enrollment->recalculateProgress();
            $enrollment->refresh();
        }

        $completedLessonIds = $enrollment
            ? $enrollment
                ->lessonCompletions
                ->pluck('lesson_id')
                ->map(fn ($id): int => (int) $id)
                ->all()
            : [];

        $lessons = $course
            ->publishedLessons
            ->values()
            ->map(function (
                Lesson $lesson,
                int $index
            ) use (
                $course,
                $enrollment,
                $completedLessonIds,
                $user
            ): Lesson {
                $lesson->setAttribute(
                    'is_completed_for_user',
                    in_array(
                        $lesson->id,
                        $completedLessonIds,
                        true
                    )
                );

                $lesson->setAttribute(
                    'is_locked_for_user',
                    $this->isLessonLocked(
                        $course,
                        $lesson,
                        $index,
                        $enrollment,
                        $completedLessonIds
                    )
                );

                $quiz = $lesson->quiz;

                $lesson->setAttribute(
                    'quiz_passed_for_user',
                    $quiz
                        ? $quiz->userHasPassed($user)
                        : false
                );

                return $lesson;
            });

        $firstAccessibleLesson = $lessons->first(
            fn (Lesson $lesson): bool =>
                ! $lesson->getAttribute(
                    'is_locked_for_user'
                )
        );

        return view('learn.course', [
            'course' => $course,
            'enrollment' => $enrollment,
            'lessons' => $lessons,
            'firstAccessibleLesson' => $firstAccessibleLesson,
        ]);
    }

    public function enroll(
        Request $request,
        Course $course
    ): RedirectResponse {
        $this->ensureCourseIsAvailable($course);

        abort_unless(
            $course->allow_self_enrolment,
            403,
            'This course is not currently open for self-enrolment.'
        );

        $user = $request->user();

        $firstLesson = $course
            ->publishedLessons()
            ->first();

        $enrollment = CourseEnrollment::query()
            ->firstOrCreate(
                [
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                ],
                [
                    'status' =>
                        CourseEnrollment::STATUS_ACTIVE,

                    'enrolled_at' => now(),
                    'last_activity_at' => now(),
                    'progress_percentage' => 0,
                    'last_lesson_id' =>
                        $firstLesson?->id,
                ]
            );

        if (
            $enrollment->status
            === CourseEnrollment::STATUS_CANCELLED
        ) {
            $enrollment->update([
                'status' =>
                    CourseEnrollment::STATUS_ACTIVE,

                'completed_at' => null,
                'last_activity_at' => now(),
            ]);
        }

        if (! $firstLesson) {
            return redirect()
                ->route(
                    'learn.courses.show',
                    $course
                )
                ->with(
                    'success',
                    'You have enrolled successfully. Lessons will appear when they are published.'
                );
        }

        return redirect()
            ->route(
                'learn.lessons.show',
                [
                    'course' => $course,
                    'lesson' => $firstLesson,
                ]
            )
            ->with(
                'success',
                'You are now enrolled in this course.'
            );
    }

    public function lesson(
        Course $course,
        Lesson $lesson
    ): View|RedirectResponse {
        $this->ensureCourseIsAvailable($course);

        $this->ensureLessonBelongsToCourse(
            $course,
            $lesson
        );

        abort_unless(
            $lesson->is_published,
            404
        );

        $user = Auth::user();

        $lesson->load([
            'quiz' => fn ($query) => $query
                ->where('is_published', true),
        ]);

        $enrollment = CourseEnrollment::query()
            ->with('lessonCompletions')
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $enrollment && ! $lesson->is_preview) {
            return redirect()
                ->route(
                    'learn.courses.show',
                    $course
                )
                ->withErrors([
                    'enrolment' =>
                        'Please enrol before opening this lesson.',
                ]);
        }

        $courseLessons = $course
            ->publishedLessons()
            ->with('quiz')
            ->get()
            ->values();

        $lessonIndex = $courseLessons->search(
            fn (Lesson $courseLesson): bool =>
                $courseLesson->id === $lesson->id
        );

        abort_if(
            $lessonIndex === false,
            404
        );

        $completedLessonIds = $enrollment
            ? $enrollment
                ->lessonCompletions
                ->pluck('lesson_id')
                ->map(fn ($id): int => (int) $id)
                ->all()
            : [];

        if (
            $this->isLessonLocked(
                $course,
                $lesson,
                (int) $lessonIndex,
                $enrollment,
                $completedLessonIds
            )
        ) {
            return redirect()
                ->route(
                    'learn.courses.show',
                    $course
                )
                ->withErrors([
                    'lesson' =>
                        'Complete the previous lesson before opening this lesson.',
                ]);
        }

        $isCompleted = in_array(
            $lesson->id,
            $completedLessonIds,
            true
        );

        $quiz = $lesson->quiz;

        $quizPassed = $quiz
            ? $quiz->userHasPassed($user)
            : false;

        $quizAttempts = $quiz
            ? $quiz
                ->attempts()
                ->where('user_id', $user->id)
                ->whereNotNull('submitted_at')
                ->latest('attempt_number')
                ->get()
            : collect();

        $canAttemptQuiz = $quiz
            ? $quiz->canUserAttempt($user)
            : false;

        $previousLesson = $lessonIndex > 0
            ? $courseLessons->get(
                $lessonIndex - 1
            )
            : null;

        $nextLesson = $courseLessons->get(
            $lessonIndex + 1
        );

        if ($enrollment) {
            $enrollment->update([
                'started_at' =>
                    $enrollment->started_at ?? now(),

                'last_lesson_id' => $lesson->id,
                'last_activity_at' => now(),
            ]);
        }

        return view('learn.lesson', [
            'course' => $course,
            'lesson' => $lesson,
            'enrollment' => $enrollment,
            'courseLessons' => $courseLessons,
            'lessonNumber' => $lessonIndex + 1,
            'lessonCount' => $courseLessons->count(),
            'previousLesson' => $previousLesson,
            'nextLesson' => $nextLesson,
            'isCompleted' => $isCompleted,
            'completedLessonIds' => $completedLessonIds,
            'quiz' => $quiz,
            'quizPassed' => $quizPassed,
            'quizAttempts' => $quizAttempts,
            'canAttemptQuiz' => $canAttemptQuiz,
        ]);
    }

    public function quiz(
        Course $course,
        Lesson $lesson
    ): View|RedirectResponse {
        $this->ensureCourseIsAvailable($course);

        $this->ensureLessonBelongsToCourse(
            $course,
            $lesson
        );

        abort_unless(
            $lesson->is_published,
            404
        );

        $user = Auth::user();

        $enrollment = CourseEnrollment::query()
            ->with('lessonCompletions')
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $enrollment) {
            return redirect()
                ->route(
                    'learn.courses.show',
                    $course
                )
                ->withErrors([
                    'quiz' =>
                        'Please enrol before taking this quiz.',
                ]);
        }

        $courseLessons = $course
            ->publishedLessons()
            ->get()
            ->values();

        $lessonIndex = $courseLessons->search(
            fn (Lesson $item): bool =>
                $item->id === $lesson->id
        );

        abort_if(
            $lessonIndex === false,
            404
        );

        $completedLessonIds = $enrollment
            ->lessonCompletions
            ->pluck('lesson_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        abort_if(
            $this->isLessonLocked(
                $course,
                $lesson,
                (int) $lessonIndex,
                $enrollment,
                $completedLessonIds
            ),
            403,
            'Complete the previous lesson first.'
        );

        $quiz = Quiz::query()
            ->published()
            ->with([
                'publishedQuestions.answers',
            ])
            ->where('lesson_id', $lesson->id)
            ->firstOrFail();

        if ($quiz->userHasPassed($user)) {
            $passedAttempt = $quiz
                ->attempts()
                ->where('user_id', $user->id)
                ->where('passed', true)
                ->latest('attempt_number')
                ->first();

            return redirect()->route(
                'learn.quiz.results',
                [
                    'course' => $course,
                    'lesson' => $lesson,
                    'attempt' => $passedAttempt,
                ]
            );
        }

        if (! $quiz->canUserAttempt($user)) {
            return redirect()
                ->route(
                    'learn.lessons.show',
                    [
                        'course' => $course,
                        'lesson' => $lesson,
                    ]
                )
                ->withErrors([
                    'quiz' =>
                        'You have used all available attempts for this quiz.',
                ]);
        }

        $questions = $quiz->publishedQuestions;

        if ($quiz->shuffle_questions) {
            $questions = $questions
                ->shuffle()
                ->values();
        }

        $questions->each(
            function (QuizQuestion $question) use (
                $quiz
            ): void {
                if ($quiz->shuffle_answers) {
                    $question->setRelation(
                        'answers',
                        $question
                            ->answers
                            ->shuffle()
                            ->values()
                    );
                }
            }
        );

        $attemptNumber = $quiz
            ->attempts()
            ->where('user_id', $user->id)
            ->count() + 1;

        return view('learn.quiz', [
            'course' => $course,
            'lesson' => $lesson,
            'quiz' => $quiz,
            'questions' => $questions,
            'attemptNumber' => $attemptNumber,
        ]);
    }

    public function submitQuiz(
        Request $request,
        Course $course,
        Lesson $lesson
    ): RedirectResponse {
        $this->ensureCourseIsAvailable($course);

        $this->ensureLessonBelongsToCourse(
            $course,
            $lesson
        );

        $user = $request->user();

        $enrollment = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $quiz = Quiz::query()
            ->published()
            ->with([
                'publishedQuestions.answers',
            ])
            ->where('lesson_id', $lesson->id)
            ->firstOrFail();

        if ($quiz->userHasPassed($user)) {
            return redirect()
                ->route(
                    'learn.lessons.show',
                    [
                        'course' => $course,
                        'lesson' => $lesson,
                    ]
                )
                ->with(
                    'success',
                    'You have already passed this quiz.'
                );
        }

        abort_unless(
            $quiz->canUserAttempt($user),
            403,
            'No quiz attempts remain.'
        );

        $submittedAnswers = $request->input(
            'answers',
            []
        );

        $missingQuestion = $quiz
            ->publishedQuestions
            ->first(
                fn (QuizQuestion $question): bool =>
                    empty(
                        $submittedAnswers[
                            $question->id
                        ] ?? []
                    )
            );

        if ($missingQuestion) {
            throw ValidationException::withMessages([
                "answers.{$missingQuestion->id}" =>
                    'Please select at least one answer for every question.',
            ]);
        }

        $attempt = DB::transaction(
            function () use (
                $quiz,
                $user,
                $enrollment,
                $submittedAnswers
            ): QuizAttempt {
                $attemptNumber = $quiz
                    ->attempts()
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->count() + 1;

                $maximumPoints = $quiz
                    ->publishedQuestions
                    ->sum('points');

                $attempt = QuizAttempt::query()
                    ->create([
                        'quiz_id' => $quiz->id,
                        'user_id' => $user->id,
                        'course_enrollment_id' =>
                            $enrollment->id,

                        'attempt_number' =>
                            $attemptNumber,

                        'score_points' => 0,
                        'maximum_points' =>
                            $maximumPoints,

                        'percentage' => 0,
                        'passed' => false,
                        'started_at' => now(),
                        'submitted_at' => now(),
                    ]);

                $scorePoints = 0;

                foreach (
                    $quiz->publishedQuestions
                    as $question
                ) {
                    $validAnswerIds = $question
                        ->answers
                        ->pluck('id')
                        ->map(fn ($id): int => (int) $id)
                        ->all();

                    $selectedIds = collect(
                        $submittedAnswers[
                            $question->id
                        ] ?? []
                    )
                        ->map(fn ($id): int => (int) $id)
                        ->filter(
                            fn (int $id): bool =>
                                in_array(
                                    $id,
                                    $validAnswerIds,
                                    true
                                )
                        )
                        ->unique()
                        ->sort()
                        ->values()
                        ->all();

                    $correctIds = $question
                        ->answers
                        ->where('is_correct', true)
                        ->pluck('id')
                        ->map(fn ($id): int => (int) $id)
                        ->unique()
                        ->sort()
                        ->values()
                        ->all();

                    /*
                     * Exact set comparison:
                     *
                     * - Selecting every correct option passes.
                     * - Missing a correct option fails.
                     * - Adding any incorrect option fails.
                     */
                    $isCorrect =
                        $selectedIds === $correctIds;

                    $pointsAwarded = $isCorrect
                        ? $question->points
                        : 0;

                    $scorePoints += $pointsAwarded;

                    QuizAttemptAnswer::query()
                        ->create([
                            'quiz_attempt_id' =>
                                $attempt->id,

                            'quiz_question_id' =>
                                $question->id,

                            /*
                             * Kept null because responses can
                             * now contain multiple answers.
                             */
                            'quiz_answer_id' => null,

                            'selected_answer_ids' =>
                                $selectedIds,

                            'is_correct' => $isCorrect,

                            'points_awarded' =>
                                $pointsAwarded,
                        ]);
                }

                $percentage = $maximumPoints > 0
                    ? (int) round(
                        (
                            $scorePoints
                            / $maximumPoints
                        ) * 100
                    )
                    : 0;

                $passed =
                    $percentage
                    >= $quiz->pass_percentage;

                $attempt->update([
                    'score_points' => $scorePoints,
                    'percentage' => $percentage,
                    'passed' => $passed,
                ]);

                $enrollment->update([
                    'last_activity_at' => now(),
                    'last_lesson_id' =>
                        $quiz->lesson_id,
                ]);

                return $attempt->fresh();
            }
        );

        return redirect()->route(
            'learn.quiz.results',
            [
                'course' => $course,
                'lesson' => $lesson,
                'attempt' => $attempt,
            ]
        );
    }

    public function quizResult(
        Course $course,
        Lesson $lesson,
        QuizAttempt $attempt
    ): View {
        $this->ensureCourseIsAvailable($course);

        $this->ensureLessonBelongsToCourse(
            $course,
            $lesson
        );

        abort_unless(
            $attempt->user_id === Auth::id(),
            403
        );

        $attempt->load([
            'quiz',
            'answers.question.answers',
        ]);

        abort_unless(
            $attempt->quiz->lesson_id
            === $lesson->id,
            404
        );

        $canAttemptAgain =
            ! $attempt->passed
            && $attempt
                ->quiz
                ->canUserAttempt(
                    Auth::user()
                );

        return view('learn.quiz-result', [
            'course' => $course,
            'lesson' => $lesson,
            'quiz' => $attempt->quiz,
            'attempt' => $attempt,
            'canAttemptAgain' => $canAttemptAgain,
        ]);
    }

    public function completeLesson(
        Request $request,
        Course $course,
        Lesson $lesson
    ): RedirectResponse {
        $this->ensureCourseIsAvailable($course);

        $this->ensureLessonBelongsToCourse(
            $course,
            $lesson
        );

        abort_unless(
            $lesson->is_published,
            404
        );

        $user = $request->user();

        $enrollment = CourseEnrollment::query()
            ->with('lessonCompletions')
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $requiredQuiz = Quiz::query()
            ->published()
            ->where('lesson_id', $lesson->id)
            ->where('is_required', true)
            ->first();

        if (
            $requiredQuiz
            && ! $requiredQuiz->userHasPassed($user)
        ) {
            return redirect()
                ->route(
                    'learn.quiz.show',
                    [
                        'course' => $course,
                        'lesson' => $lesson,
                    ]
                )
                ->withErrors([
                    'quiz' =>
                        'Pass the lesson quiz before marking this lesson complete.',
                ]);
        }

        $courseLessons = $course
            ->publishedLessons()
            ->get()
            ->values();

        $lessonIndex = $courseLessons->search(
            fn (Lesson $courseLesson): bool =>
                $courseLesson->id === $lesson->id
        );

        abort_if(
            $lessonIndex === false,
            404
        );

        $completedLessonIds = $enrollment
            ->lessonCompletions
            ->pluck('lesson_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        abort_if(
            $this->isLessonLocked(
                $course,
                $lesson,
                (int) $lessonIndex,
                $enrollment,
                $completedLessonIds
            ),
            403,
            'Complete the previous lesson first.'
        );

        $nextLesson = $courseLessons->get(
            $lessonIndex + 1
        );

        DB::transaction(
            function () use (
                $user,
                $lesson,
                $enrollment,
                $nextLesson
            ): void {
                LessonCompletion::query()
                    ->firstOrCreate(
                        [
                            'lesson_id' => $lesson->id,
                            'user_id' => $user->id,
                        ],
                        [
                            'course_enrollment_id' =>
                                $enrollment->id,

                            'started_at' =>
                                $enrollment->started_at
                                ?? now(),

                            'completed_at' => now(),
                        ]
                    );

                $enrollment->refresh();

                $enrollment->update([
                    'last_lesson_id' =>
                        $nextLesson?->id
                        ?? $lesson->id,

                    'last_activity_at' => now(),
                ]);

                $enrollment->recalculateProgress();
            }
        );

        $enrollment->refresh();

        if (
            $enrollment->status
            === CourseEnrollment::STATUS_COMPLETED
        ) {
            return redirect()
                ->route(
                    'learn.courses.show',
                    $course
                )
                ->with(
                    'success',
                    'Congratulations! You have completed this course.'
                );
        }

        if ($nextLesson) {
            return redirect()
                ->route(
                    'learn.lessons.show',
                    [
                        'course' => $course,
                        'lesson' => $nextLesson,
                    ]
                )
                ->with(
                    'success',
                    'Lesson completed. Continue to the next lesson.'
                );
        }

        return redirect()
            ->route(
                'learn.courses.show',
                $course
            )
            ->with(
                'success',
                'Lesson completed successfully.'
            );
    }

    private function ensureCourseIsAvailable(
        Course $course
    ): void {
        abort_unless(
            $course->is_published
            && (
                blank($course->published_at)
                || $course->published_at->isPast()
            ),
            404
        );
    }

    private function ensureLessonBelongsToCourse(
        Course $course,
        Lesson $lesson
    ): void {
        abort_unless(
            $lesson->course_id === $course->id,
            404
        );
    }

    private function isLessonLocked(
        Course $course,
        Lesson $lesson,
        int $lessonIndex,
        ?CourseEnrollment $enrollment,
        array $completedLessonIds
    ): bool {
        if ($lesson->is_preview) {
            return false;
        }

        if (! $enrollment) {
            return true;
        }

        if (! $course->requires_sequential_progress) {
            return false;
        }

        if ($lessonIndex === 0) {
            return false;
        }

        $courseLessons = $course->relationLoaded(
            'publishedLessons'
        )
            ? $course
                ->publishedLessons
                ->values()
            : $course
                ->publishedLessons()
                ->get()
                ->values();

        $previousLesson = $courseLessons->get(
            $lessonIndex - 1
        );

        if (! $previousLesson) {
            return false;
        }

        return ! in_array(
            $previousLesson->id,
            $completedLessonIds,
            true
        );
    }

    public function accessRestricted(
        Course $course
    ): View|RedirectResponse {
        $this->ensureCourseIsAvailable($course);

        $enrollment = CourseEnrollment::query()
            ->with([
                'course',
                'pausedByUser',
            ])
            ->where('course_id', $course->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (! $enrollment->blocksLearningAccess()) {
            return redirect()->route(
                'learn.courses.show',
                $course
            );
        }

        return view(
            'learn.access-restricted',
            [
                'course' => $course,
                'enrollment' => $enrollment,
            ]
        );
    }
}