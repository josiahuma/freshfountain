@extends('layouts.site')

@section('content')

<section class="bg-slate-950 py-14 text-white md:py-20">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-extrabold uppercase tracking-[0.16em] text-blue-300">
                    Fresh Learning
                </p>

                <h1 class="mt-3 text-3xl font-extrabold md:text-5xl">
                    Welcome, {{ Str::before($user->name, ' ') }}
                </h1>

                <p class="mt-4 max-w-2xl text-slate-300">
                    Continue your learning, track your progress
                    and complete your Fresh Fountain courses.
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="rounded-xl border border-white/20 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-white/10"
                >
                    Sign out
                </button>
            </form>
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

        <div class="grid gap-5 sm:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-bold text-slate-500">
                    My courses
                </p>

                <p class="mt-2 text-4xl font-extrabold text-slate-950">
                    {{ $enrollments->count() }}
                </p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-bold text-slate-500">
                    In progress
                </p>

                <p class="mt-2 text-4xl font-extrabold text-blue-700">
                    {{ $enrollments->where('status', 'active')->count() }}
                </p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-bold text-slate-500">
                    Completed
                </p>

                <p class="mt-2 text-4xl font-extrabold text-emerald-600">
                    {{ $enrollments->where('status', 'completed')->count() }}
                </p>
            </div>
        </div>

        <div class="mt-12">
            <h2 class="text-2xl font-extrabold text-slate-950">
                My courses
            </h2>

            @if($enrollments->isEmpty())
                <div class="mt-5 rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-slate-600">
                    You have not enrolled in a course yet.
                    Available courses will appear below.
                </div>
            @else
                <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($enrollments as $enrollment)
                        <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                            <div class="p-6">
                                <p class="text-xs font-extrabold uppercase tracking-wide text-blue-700">
                                    {{ $enrollment->course->course_type }}
                                </p>

                                <h3 class="mt-3 text-xl font-extrabold text-slate-950">
                                    {{ $enrollment->course->title }}
                                </h3>

                                <div class="mt-6">
                                    <div class="flex justify-between text-sm font-bold">
                                        <span class="text-slate-600">
                                            Progress
                                        </span>

                                        <span class="text-blue-700">
                                            {{ $enrollment->progress_percentage }}%
                                        </span>
                                    </div>

                                    <div class="mt-2 h-3 overflow-hidden rounded-full bg-slate-200">
                                        <div
                                            class="h-full rounded-full bg-blue-700"
                                            style="width: {{ $enrollment->progress_percentage }}%"
                                        ></div>
                                    </div>
                                </div>

                                <div class="mt-6 flex flex-wrap items-center gap-4">
                                    @if(
                                        $enrollment->status
                                        === \App\Models\CourseEnrollment::STATUS_COMPLETED
                                        && $enrollment->certificate
                                        && ! $enrollment->certificate->isRevoked()
                                    )
                                        <a
                                            href="{{ route(
                                                'certificates.download',
                                                $enrollment->certificate
                                            ) }}"
                                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-emerald-800"
                                        >
                                            Download certificate
                                            <span>↓</span>
                                        </a>

                                        <a
                                            href="{{ route(
                                                'certificates.stream',
                                                $enrollment->certificate
                                            ) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-2 text-sm font-extrabold text-blue-700 transition hover:text-blue-900"
                                        >
                                            View certificate
                                            <span>→</span>
                                        </a>
                                    @elseif(
                                        $enrollment->status
                                        === \App\Models\CourseEnrollment::STATUS_PAUSED
                                    )
                                        <a
                                            href="{{ route(
                                                'learn.courses.access-restricted',
                                                $enrollment->course
                                            ) }}"
                                            class="inline-flex items-center gap-2 text-sm font-extrabold text-amber-700 transition hover:text-amber-900"
                                        >
                                            View access status
                                            <span>→</span>
                                        </a>
                                    @else
                                        <a
                                            href="{{ route(
                                                'learn.courses.show',
                                                $enrollment->course
                                            ) }}"
                                            class="inline-flex items-center gap-2 text-sm font-extrabold text-blue-700 transition hover:text-blue-900"
                                        >
                                            @if($enrollment->progress_percentage > 0)
                                                Continue course
                                            @else
                                                Start course
                                            @endif

                                            <span>→</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-14">
            <h2 class="text-2xl font-extrabold text-slate-950">
                Available courses
            </h2>

            <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse($availableCourses as $course)
                    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        @if($course->cover_image_url)
                            <img
                                src="{{ $course->cover_image_url }}"
                                alt="{{ $course->title }}"
                                class="h-48 w-full object-cover"
                            >
                        @endif

                        <div class="p-6">
                            <p class="text-xs font-extrabold uppercase tracking-wide text-blue-700">
                                {{ \App\Models\Course::typeOptions()[$course->course_type] ?? 'Course' }}
                            </p>

                            <h3 class="mt-3 text-xl font-extrabold text-slate-950">
                                {{ $course->title }}
                            </h3>

                            @if($course->short_description)
                                <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">
                                    {{ $course->short_description }}
                                </p>
                            @endif

                            <div class="mt-5 flex flex-wrap gap-3 text-xs font-bold text-slate-500">
                                <span>
                                    {{ $course->published_lessons_count }}
                                    {{ Str::plural('lesson', $course->published_lessons_count) }}
                                </span>

                                @if($course->estimated_duration_label)
                                    <span>
                                        {{ $course->estimated_duration_label }}
                                    </span>
                                @endif
                            </div>

                            <a
                                href="{{ route('learn.courses.show', $course) }}"
                                class="mt-6 inline-flex items-center gap-2 text-sm font-extrabold text-blue-700 transition hover:text-blue-900"
                            >
                                View course

                                <span>→</span>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-slate-600 md:col-span-2">
                        No additional published courses are available yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

@endsection