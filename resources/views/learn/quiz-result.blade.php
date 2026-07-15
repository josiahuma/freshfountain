@extends('layouts.site')

@section('content')

<section class="{{ $attempt->passed ? 'bg-emerald-950' : 'bg-slate-950' }} py-12 text-white md:py-16">
    <div class="mx-auto max-w-5xl px-4 text-center sm:px-6 lg:px-8">
        <p class="text-sm font-extrabold uppercase tracking-[0.16em] {{ $attempt->passed ? 'text-emerald-300' : 'text-blue-300' }}">
            Quiz result
        </p>

        <h1 class="mt-4 text-4xl font-extrabold md:text-6xl">
            {{ $attempt->passed ? 'Congratulations!' : 'Keep learning' }}
        </h1>

        <div class="mx-auto mt-8 flex h-40 w-40 flex-col items-center justify-center rounded-full border-8 {{ $attempt->passed ? 'border-emerald-400 bg-emerald-900' : 'border-blue-400 bg-slate-900' }}">
            <span class="text-4xl font-extrabold">
                {{ $attempt->percentage }}%
            </span>

            <span class="mt-1 text-xs font-extrabold uppercase tracking-wide">
                {{ $attempt->passed ? 'Passed' : 'Not passed' }}
            </span>
        </div>

        <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-200">
            You scored {{ $attempt->score_points }}
            out of {{ $attempt->maximum_points }} points.

            The required pass mark is
            {{ $quiz->pass_percentage }}%.
        </p>
    </div>
</section>

<section class="bg-slate-100 py-10 md:py-14">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-slate-500">
                    Attempt {{ $attempt->attempt_number }}
                </p>

                <p class="mt-1 text-xl font-extrabold text-slate-950">
                    {{ $quiz->title }}
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a
                    href="{{ route('learn.lessons.show', [
                        'course' => $course,
                        'lesson' => $lesson,
                    ]) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 font-extrabold text-slate-800 transition hover:border-blue-500 hover:text-blue-700"
                >
                    Return to lesson
                </a>

                @if($canAttemptAgain)
                    <a
                        href="{{ route('learn.quiz.show', [
                            'course' => $course,
                            'lesson' => $lesson,
                        ]) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-5 py-3 font-extrabold text-white transition hover:bg-blue-800"
                    >
                        Try again
                    </a>
                @endif
            </div>
        </div>

        @if($quiz->show_correct_answers)
            <div class="mt-8 space-y-6">
                @foreach($attempt->answers as $index => $attemptAnswer)
                    @php
                        $question =
                            $attemptAnswer->question;

                        $selectedIds =
                            $attemptAnswer->selectedIds();

                        $correctIds = $question
                            ->answers
                            ->where('is_correct', true)
                            ->pluck('id')
                            ->map(fn ($id) => (int) $id)
                            ->all();
                    @endphp

                    <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $attemptAnswer->is_correct ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} font-extrabold">
                                {{ $attemptAnswer->is_correct ? '✓' : '×' }}
                            </span>

                            <div>
                                <h2 class="text-lg font-extrabold text-slate-950">
                                    {{ $question->question }}
                                </h2>

                                <p class="mt-2 text-sm font-bold {{ $attemptAnswer->is_correct ? 'text-emerald-700' : 'text-red-700' }}">
                                    {{ $attemptAnswer->is_correct ? 'Correct' : 'Incorrect' }}
                                    ·
                                    {{ $attemptAnswer->points_awarded }}
                                    / {{ $question->points }}
                                    points
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            @foreach($question->answers as $answer)
                                @php
                                    $selected = in_array(
                                        $answer->id,
                                        $selectedIds,
                                        true
                                    );

                                    $correct = in_array(
                                        $answer->id,
                                        $correctIds,
                                        true
                                    );
                                @endphp

                                <div class="rounded-2xl border p-4
                                    @if($correct)
                                        border-emerald-300 bg-emerald-50
                                    @elseif($selected)
                                        border-red-300 bg-red-50
                                    @else
                                        border-slate-200 bg-white
                                    @endif
                                ">
                                    <div class="flex items-start justify-between gap-4">
                                        <span class="font-semibold text-slate-800">
                                            {{ $answer->answer }}
                                        </span>

                                        <div class="flex shrink-0 flex-wrap gap-2">
                                            @if($selected)
                                                <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-extrabold text-blue-700">
                                                    Your answer
                                                </span>
                                            @endif

                                            @if($correct)
                                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-extrabold text-emerald-700">
                                                    Correct
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($question->explanation)
                            <div class="mt-5 rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                    Explanation
                                </p>

                                <p class="mt-2 leading-7 text-slate-700">
                                    {{ $question->explanation }}
                                </p>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

@endsection