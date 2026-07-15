@extends('layouts.site')

@section('content')

<section class="relative overflow-hidden bg-slate-950 py-14 text-white md:py-20">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-950 via-slate-950 to-violet-950"></div>

    <div class="relative mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
        <a
            href="{{ route('learn.dashboard') }}"
            class="inline-flex items-center gap-2 text-sm font-extrabold text-blue-300 transition hover:text-white"
        >
            ← Back to learning dashboard
        </a>

        <div class="mt-8 grid gap-10 md:grid-cols-[minmax(0,1fr)_420px] md:items-center">
            <div>
                <p class="text-sm font-extrabold uppercase tracking-[0.16em] text-blue-300">
                    {{ \App\Models\Course::typeOptions()[$course->course_type] ?? 'Course' }}
                </p>

                <h1 class="mt-4 text-4xl font-extrabold leading-tight md:text-6xl">
                    {{ $course->title }}
                </h1>

                @if($course->short_description)
                    <p class="mt-5 max-w-3xl text-lg leading-8 text-slate-300">
                        {{ $course->short_description }}
                    </p>
                @endif

                <div class="mt-7 flex flex-wrap gap-3 text-sm font-bold">
                    <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2">
                        {{ $lessons->count() }}
                        {{ Str::plural('lesson', $lessons->count()) }}
                    </span>

                    @if($course->estimated_duration_label)
                        <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2">
                            {{ $course->estimated_duration_label }}
                        </span>
                    @endif

                    <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2">
                        {{ \App\Models\Course::difficultyOptions()[$course->difficulty_level] ?? ucfirst($course->difficulty_level) }}
                    </span>
                </div>
            </div>

            @if($course->cover_image_url)
                <div class="w-full min-w-0 max-w-[420px] justify-self-end overflow-hidden rounded-3xl border border-white/10 shadow-2xl">
                    <img
                        src="{{ $course->cover_image_url }}"
                        alt="{{ $course->title }}"
                        class="block aspect-video h-auto w-full object-cover"
                    >
                </div>
            @endif
        </div>
    </div>
</section>

<section class="bg-slate-50 py-10 md:py-16">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-7 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-7 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-8 md:grid-cols-[minmax(0,1fr)_360px]">
            <div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-9">
                    <h2 class="text-2xl font-extrabold text-slate-950">
                        About this course
                    </h2>

                    @if($course->description)
                        <div class="prose prose-slate mt-6 max-w-none">
                            {!! $course->description !!}
                        </div>
                    @elseif($course->short_description)
                        <p class="mt-5 leading-7 text-slate-600">
                            {{ $course->short_description }}
                        </p>
                    @else
                        <p class="mt-5 text-slate-600">
                            Course information will be added shortly.
                        </p>
                    @endif
                </div>

                <div class="mt-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-extrabold text-slate-950">
                                Course lessons
                            </h2>

                            <p class="mt-1 text-sm text-slate-600">
                                Work through each lesson and track your progress.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse($lessons as $index => $lesson)
                            @php
                                $isCompleted = $lesson->getAttribute(
                                    'is_completed_for_user'
                                );

                                $isLocked = $lesson->getAttribute(
                                    'is_locked_for_user'
                                );
                            @endphp

                            @if($isLocked)
                                <div class="flex items-center gap-5 rounded-2xl border border-slate-200 bg-slate-100 p-5 opacity-75">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-slate-200 font-extrabold text-slate-600">
                                        {{ $index + 1 }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-extrabold text-slate-800">
                                            {{ $lesson->title }}
                                        </h3>

                                        <p class="mt-1 text-sm font-semibold text-slate-500">
                                            Complete the previous lesson to unlock.
                                        </p>
                                    </div>

                                    <span class="text-xl" aria-label="Locked">
                                        🔒
                                    </span>
                                </div>
                            @else
                                <a
                                    href="{{ route('learn.lessons.show', [
                                        'course' => $course,
                                        'lesson' => $lesson,
                                    ]) }}"
                                    class="group flex items-center gap-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md"
                                >
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full {{ $isCompleted ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-50 text-blue-700' }} font-extrabold">
                                        @if($isCompleted)
                                            ✓
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-extrabold text-slate-950 transition group-hover:text-blue-700">
                                                {{ $lesson->title }}
                                            </h3>

                                            @if($lesson->is_preview && ! $enrollment)
                                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wide text-blue-700">
                                                    Preview
                                                </span>
                                            @endif
                                        </div>

                                        @if($lesson->summary)
                                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">
                                                {{ $lesson->summary }}
                                            </p>
                                        @endif

                                        @if($lesson->video_duration_minutes)
                                            <p class="mt-2 text-xs font-bold text-slate-500">
                                                {{ $lesson->video_duration_minutes }} min video
                                            </p>
                                        @endif
                                    </div>

                                    <span class="font-extrabold text-blue-700 transition group-hover:translate-x-1">
                                        →
                                    </span>
                                </a>
                            @endif
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-slate-600">
                                No lessons have been published for this course yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <aside>
                <div class="sticky top-24 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    @if($enrollment)
                        <p class="text-sm font-extrabold uppercase tracking-wide text-blue-700">
                            Your progress
                        </p>

                        <div class="mt-4 flex items-end justify-between">
                            <span class="text-4xl font-extrabold text-slate-950">
                                {{ $enrollment->progress_percentage }}%
                            </span>

                            <span class="text-sm font-bold text-slate-500">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </div>

                        <div class="mt-4 h-3 overflow-hidden rounded-full bg-slate-200">
                            <div
                                class="h-full rounded-full bg-blue-700 transition-all"
                                style="width: {{ $enrollment->progress_percentage }}%"
                            ></div>
                        </div>

                        @if(
                            $enrollment->status
                            === \App\Models\CourseEnrollment::STATUS_COMPLETED
                        )
                            <div class="mt-6 rounded-2xl bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
                                Course completed successfully.
                            </div>

                            @if(
                                $enrollment->certificate
                                && ! $enrollment
                                    ->certificate
                                    ->isRevoked()
                            )
                                <a
                                    href="{{ route(
                                        'certificates.download',
                                        $enrollment->certificate
                                    ) }}"
                                    class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-emerald-700 px-5 py-3.5 font-extrabold text-white transition hover:bg-emerald-800"
                                >
                                    Download certificate
                                </a>

                                <a
                                    href="{{ route(
                                        'certificates.stream',
                                        $enrollment->certificate
                                    ) }}"
                                    target="_blank"
                                    class="mt-3 inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3.5 font-extrabold text-slate-800 transition hover:border-blue-500 hover:text-blue-700"
                                >
                                    View certificate
                                </a>
                            @endif
                        @elseif($enrollment->lastLesson)
                            <a
                                href="{{ route('learn.lessons.show', [
                                    'course' => $course,
                                    'lesson' => $enrollment->lastLesson,
                                ]) }}"
                                class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-[rgb(var(--brand))] px-5 py-3.5 font-extrabold text-white transition hover:bg-[rgb(var(--brand-dark))]"
                            >
                                Continue learning
                            </a>
                        @elseif($firstAccessibleLesson)
                            <a
                                href="{{ route('learn.lessons.show', [
                                    'course' => $course,
                                    'lesson' => $firstAccessibleLesson,
                                ]) }}"
                                class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-[rgb(var(--brand))] px-5 py-3.5 font-extrabold text-white transition hover:bg-[rgb(var(--brand-dark))]"
                            >
                                Start course
                            </a>
                        @endif
                    @elseif($course->allow_self_enrolment)
                        <p class="text-sm font-extrabold uppercase tracking-wide text-blue-700">
                            Start learning
                        </p>

                        <h3 class="mt-3 text-2xl font-extrabold text-slate-950">
                            Enrol in this course
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Your progress will be saved automatically as you complete each lesson.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('learn.courses.enroll', $course) }}"
                            class="mt-6"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-[rgb(var(--brand))] px-5 py-3.5 font-extrabold text-white transition hover:bg-[rgb(var(--brand-dark))]"
                            >
                                Enrol now
                            </button>
                        </form>
                    @else
                        <p class="text-sm font-extrabold uppercase tracking-wide text-slate-500">
                            Enrolment
                        </p>

                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Please contact the church office for access to this course.
                        </p>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>

@endsection