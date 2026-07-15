@extends('layouts.site')

@section('content')

<section class="relative overflow-hidden bg-slate-950 py-14 text-white md:py-20">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950"></div>

    <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border border-white/15 bg-white/10 text-4xl">
            @if(
                $enrollment->status
                === \App\Models\CourseEnrollment::STATUS_PAUSED
            )
                ⏸
            @else
                🔒
            @endif
        </div>

        <p class="mt-8 text-sm font-extrabold uppercase tracking-[0.16em] text-blue-300">
            Fresh Learning
        </p>

        <h1 class="mt-4 text-4xl font-extrabold md:text-6xl">
            @if(
                $enrollment->status
                === \App\Models\CourseEnrollment::STATUS_PAUSED
            )
                Course access paused
            @else
                Course access unavailable
            @endif
        </h1>

        <p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-slate-300">
            @if(
                $enrollment->status
                === \App\Models\CourseEnrollment::STATUS_PAUSED
            )
                Your access to this course has been temporarily paused by a course administrator.
            @else
                Your enrolment in this course is no longer active.
            @endif
        </p>
    </div>
</section>

<section class="min-h-[50vh] bg-slate-100 py-10 md:py-16">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-6 md:p-8">
                <p class="text-xs font-extrabold uppercase tracking-[0.15em] text-blue-700">
                    Course
                </p>

                <h2 class="mt-3 text-2xl font-extrabold text-slate-950">
                    {{ $course->title }}
                </h2>
            </div>

            <div class="space-y-6 p-6 md:p-8">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                            Current status
                        </p>

                        <p class="mt-2 font-extrabold text-slate-950">
                            {{ ucfirst($enrollment->status) }}
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                            Progress saved
                        </p>

                        <p class="mt-2 font-extrabold text-slate-950">
                            {{ $enrollment->progress_percentage }}%
                        </p>
                    </div>
                </div>

                @if($enrollment->pause_reason)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                        <p class="text-xs font-extrabold uppercase tracking-wide text-amber-700">
                            Reason provided
                        </p>

                        <p class="mt-3 whitespace-pre-line leading-7 text-amber-950">
                            {{ $enrollment->pause_reason }}
                        </p>
                    </div>
                @endif

                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
                    <p class="font-extrabold text-blue-950">
                        Your progress has not been deleted
                    </p>

                    <p class="mt-2 text-sm leading-6 text-blue-800">
                        Your completed lessons and quiz results remain saved. You can continue from where you stopped after the enrolment is reactivated.
                    </p>
                </div>

                <p class="text-sm leading-7 text-slate-600">
                    Please contact Fresh Fountain if you believe this restriction was applied in error or you would like your learning access restored.
                </p>

                <a
                    href="{{ route('learn.dashboard') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-[rgb(var(--brand))] px-6 py-3.5 font-extrabold text-white transition hover:bg-[rgb(var(--brand-dark))]"
                >
                    Return to learning dashboard
                </a>
            </div>
        </div>
    </div>
</section>

@endsection