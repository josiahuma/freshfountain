@extends('layouts.site')

@section('content')

<section class="bg-slate-950 py-8 text-white">
    <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">
        <a
            href="{{ route('learn.courses.show', $course) }}"
            class="inline-flex items-center gap-2 text-sm font-extrabold text-blue-300 transition hover:text-white"
        >
            ← Back to {{ $course->title }}
        </a>

        <div class="mt-5 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-extrabold uppercase tracking-[0.14em] text-blue-300">
                    Lesson {{ $lessonNumber }} of {{ $lessonCount }}
                </p>

                <h1 class="mt-2 text-3xl font-extrabold leading-tight md:text-5xl">
                    {{ $lesson->title }}
                </h1>
            </div>

            @if($enrollment)
                <div class="text-sm font-bold text-slate-300">
                    {{ $enrollment->progress_percentage }}% complete
                </div>
            @endif
        </div>
    </div>
</section>

<section class="bg-slate-100 py-8 md:py-12">
    <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-7 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-7 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 font-semibold text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_340px]">
            <main>
                @if($lesson->embed_url)
                    <div class="overflow-hidden rounded-3xl bg-black shadow-2xl">
                        <div class="aspect-video">
                            <iframe
                                src="{{ $lesson->embed_url }}"
                                title="{{ $lesson->title }}"
                                class="h-full w-full"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen
                            ></iframe>
                        </div>
                    </div>
                @endif

                <article class="mt-7 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-10">
                    @if($lesson->summary)
                        <p class="text-lg font-semibold leading-8 text-slate-700">
                            {{ $lesson->summary }}
                        </p>
                    @endif

                    @if($lesson->content)
                        <div class="prose prose-slate mt-8 max-w-none">
                            {!! $lesson->content !!}
                        </div>
                    @elseif(! $lesson->summary)
                        <p class="text-slate-600">
                            Lesson notes will be added shortly.
                        </p>
                    @endif

                    {{-- Lesson quiz --}}
                    @if($quiz)
                        <div
                            class="mt-10 rounded-3xl border p-6
                                {{ $quizPassed
                                    ? 'border-emerald-200 bg-emerald-50'
                                    : 'border-blue-200 bg-blue-50'
                                }}"
                        >
                            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p
                                        class="text-xs font-extrabold uppercase tracking-[0.14em]
                                            {{ $quizPassed
                                                ? 'text-emerald-700'
                                                : 'text-blue-700'
                                            }}"
                                    >
                                        Lesson quiz
                                    </p>

                                    <h2 class="mt-2 text-xl font-extrabold text-slate-950">
                                        {{ $quiz->title }}
                                    </h2>

                                    <p class="mt-2 text-sm leading-6 text-slate-600">
                                        Pass mark: {{ $quiz->pass_percentage }}%.

                                        @if($quiz->maximum_attempts)
                                            {{ $quiz->maximum_attempts }}
                                            {{ Str::plural('attempt', $quiz->maximum_attempts) }}
                                            allowed.
                                        @else
                                            Unlimited attempts.
                                        @endif
                                    </p>

                                    @if($quiz->is_required)
                                        <p class="mt-2 text-sm font-bold text-amber-700">
                                            This quiz must be passed before the lesson can be completed.
                                        </p>
                                    @endif
                                </div>

                                <div class="shrink-0">
                                    @if($quizPassed)
                                        <span class="inline-flex items-center justify-center rounded-xl bg-emerald-700 px-5 py-3 font-extrabold text-white">
                                            ✓ Quiz passed
                                        </span>
                                    @elseif($canAttemptQuiz)
                                        <a
                                            href="{{ route('learn.quiz.show', [
                                                'course' => $course,
                                                'lesson' => $lesson,
                                            ]) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-5 py-3 font-extrabold text-white transition hover:bg-blue-800"
                                        >
                                            Take quiz
                                        </a>
                                    @else
                                        <span class="inline-flex rounded-xl bg-red-100 px-4 py-3 text-sm font-extrabold text-red-700">
                                            No attempts remaining
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($quizAttempts->isNotEmpty())
                                <div class="mt-5 border-t border-slate-200 pt-5">
                                    <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        Previous attempts
                                    </p>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach($quizAttempts as $previousAttempt)
                                            <a
                                                href="{{ route('learn.quiz.results', [
                                                    'course' => $course,
                                                    'lesson' => $lesson,
                                                    'attempt' => $previousAttempt,
                                                ]) }}"
                                                class="rounded-full border px-3 py-1.5 text-xs font-extrabold
                                                    {{ $previousAttempt->passed
                                                        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                        : 'border-red-200 bg-red-50 text-red-700'
                                                    }}"
                                            >
                                                Attempt {{ $previousAttempt->attempt_number }}:
                                                {{ $previousAttempt->percentage }}%
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Completion area --}}
                    @if($enrollment)
                        <div class="mt-10 border-t border-slate-200 pt-8">
                            @if($isCompleted)
                                <div class="flex flex-col gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-extrabold text-emerald-900">
                                            Lesson completed
                                        </p>

                                        <p class="mt-1 text-sm text-emerald-700">
                                            Your progress has been saved.
                                        </p>
                                    </div>

                                    @if($nextLesson)
                                        <a
                                            href="{{ route('learn.lessons.show', [
                                                'course' => $course,
                                                'lesson' => $nextLesson,
                                            ]) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-emerald-700 px-5 py-3 font-extrabold text-white transition hover:bg-emerald-800"
                                        >
                                            Next lesson →
                                        </a>
                                    @endif
                                </div>
                            @else
                                @if(
                                    $quiz
                                    && $quiz->is_required
                                    && ! $quizPassed
                                )
                                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                                        <p class="font-extrabold text-amber-900">
                                            Quiz required
                                        </p>

                                        <p class="mt-2 text-sm leading-6 text-amber-800">
                                            Pass the lesson quiz before marking this lesson complete.
                                        </p>

                                        @if($canAttemptQuiz)
                                            <a
                                                href="{{ route('learn.quiz.show', [
                                                    'course' => $course,
                                                    'lesson' => $lesson,
                                                ]) }}"
                                                class="mt-4 inline-flex items-center justify-center rounded-xl bg-amber-700 px-5 py-3 font-extrabold text-white transition hover:bg-amber-800"
                                            >
                                                Take required quiz
                                            </a>
                                        @else
                                            <p class="mt-4 text-sm font-extrabold text-red-700">
                                                No quiz attempts remain. Please contact an administrator.
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    <form
                                        method="POST"
                                        action="{{ route('learn.lessons.complete', [
                                            'course' => $course,
                                            'lesson' => $lesson,
                                        ]) }}"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="inline-flex w-full items-center justify-center rounded-xl bg-[rgb(var(--brand))] px-6 py-4 text-lg font-extrabold text-white transition hover:bg-[rgb(var(--brand-dark))] sm:w-auto"
                                        >
                                            Mark lesson complete
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    @elseif($lesson->is_preview)
                        <div class="mt-10 rounded-2xl border border-blue-200 bg-blue-50 p-5">
                            <p class="font-extrabold text-blue-900">
                                This is a preview lesson
                            </p>

                            <p class="mt-2 text-sm leading-6 text-blue-800">
                                Enrol in the course to save progress and unlock the remaining lessons.
                            </p>

                            <form
                                method="POST"
                                action="{{ route('learn.courses.enroll', $course) }}"
                                class="mt-4"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="rounded-xl bg-blue-700 px-5 py-3 font-extrabold text-white transition hover:bg-blue-800"
                                >
                                    Enrol in course
                                </button>
                            </form>
                        </div>
                    @endif
                </article>

                {{-- Previous and next navigation --}}
                <nav class="mt-7 flex flex-col gap-4 sm:flex-row sm:justify-between">
                    @if($previousLesson)
                        <a
                            href="{{ route('learn.lessons.show', [
                                'course' => $course,
                                'lesson' => $previousLesson,
                            ]) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 font-extrabold text-slate-800 transition hover:border-blue-500 hover:text-blue-700"
                        >
                            ← Previous lesson
                        </a>
                    @else
                        <span></span>
                    @endif

                    @if($nextLesson && $isCompleted)
                        <a
                            href="{{ route('learn.lessons.show', [
                                'course' => $course,
                                'lesson' => $nextLesson,
                            ]) }}"
                            class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-5 py-3 font-extrabold text-white transition hover:bg-blue-800"
                        >
                            Next lesson →
                        </a>
                    @endif
                </nav>
            </main>

            {{-- Course curriculum --}}
            <aside>
                <div class="sticky top-24 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 p-5">
                        <p class="text-sm font-extrabold uppercase tracking-wide text-blue-700">
                            Course curriculum
                        </p>

                        <h2 class="mt-2 text-lg font-extrabold text-slate-950">
                            {{ $course->title }}
                        </h2>
                    </div>

                    <div class="max-h-[65vh] overflow-y-auto p-3">
                        @foreach($courseLessons as $index => $courseLesson)
                            @php
                                $completed = in_array(
                                    $courseLesson->id,
                                    $completedLessonIds,
                                    true
                                );

                                $isCurrent =
                                    $courseLesson->id === $lesson->id;

                                $previousCourseLesson =
                                    $courseLessons->get($index - 1);

                                $locked =
                                    $course->requires_sequential_progress
                                    && $index > 0
                                    && ! $courseLesson->is_preview
                                    && ! in_array(
                                        $previousCourseLesson?->id,
                                        $completedLessonIds,
                                        true
                                    );
                            @endphp

                            @if($locked)
                                <div class="mb-2 flex gap-3 rounded-xl bg-slate-100 p-3 opacity-65">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-extrabold text-slate-600">
                                        {{ $index + 1 }}
                                    </span>

                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-700">
                                            {{ $courseLesson->title }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Locked
                                        </p>
                                    </div>
                                </div>
                            @else
                                <a
                                    href="{{ route('learn.lessons.show', [
                                        'course' => $course,
                                        'lesson' => $courseLesson,
                                    ]) }}"
                                    class="mb-2 flex gap-3 rounded-xl p-3 transition
                                        {{ $isCurrent
                                            ? 'bg-blue-50 ring-1 ring-blue-200'
                                            : 'hover:bg-slate-50'
                                        }}"
                                >
                                    <span
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-extrabold
                                            {{ $completed
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-slate-100 text-slate-700'
                                            }}"
                                    >
                                        {{ $completed ? '✓' : $index + 1 }}
                                    </span>

                                    <div class="min-w-0">
                                        <p
                                            class="text-sm font-extrabold
                                                {{ $isCurrent
                                                    ? 'text-blue-800'
                                                    : 'text-slate-900'
                                                }}"
                                        >
                                            {{ $courseLesson->title }}
                                        </p>

                                        @if($courseLesson->video_duration_minutes)
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $courseLesson->video_duration_minutes }} min
                                            </p>
                                        @endif

                                        @if(
                                            $courseLesson->quiz
                                            && $courseLesson->quiz->is_published
                                        )
                                            <p class="mt-1 text-xs font-bold text-violet-600">
                                                Includes quiz
                                            </p>
                                        @endif
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

@endsection