@extends('layouts.site')

@section('content')

<section class="bg-slate-950 py-10 text-white md:py-14">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <a
            href="{{ route('learn.lessons.show', [
                'course' => $course,
                'lesson' => $lesson,
            ]) }}"
            class="inline-flex items-center gap-2 text-sm font-extrabold text-blue-300 transition hover:text-white"
        >
            ← Back to lesson
        </a>

        <p class="mt-8 text-sm font-extrabold uppercase tracking-[0.15em] text-blue-300">
            {{ $course->title }}
        </p>

        <h1 class="mt-3 text-3xl font-extrabold md:text-5xl">
            {{ $quiz->title }}
        </h1>

        <div class="mt-5 flex flex-wrap gap-3 text-sm font-bold">
            <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2">
                {{ $questions->count() }}
                {{ Str::plural('question', $questions->count()) }}
            </span>

            <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2">
                Pass mark: {{ $quiz->pass_percentage }}%
            </span>

            <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2">
                Attempt {{ $attemptNumber }}
            </span>
        </div>
    </div>
</section>

<section class="bg-slate-100 py-10 md:py-14">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

        @if($errors->any())
            <div class="mb-7 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 font-semibold text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        @if($quiz->description)
            <div class="mb-8 rounded-3xl border border-blue-200 bg-blue-50 p-6 text-blue-900">
                <h2 class="font-extrabold">
                    Quiz instructions
                </h2>

                <p class="mt-2 leading-7">
                    {{ $quiz->description }}
                </p>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('learn.quiz.submit', [
                'course' => $course,
                'lesson' => $lesson,
            ]) }}"
        >
            @csrf

            <div class="space-y-7">
                @foreach($questions as $questionIndex => $question)
                    @php
                        $multipleCorrect =
                            $question
                                ->answers
                                ->where('is_correct', true)
                                ->count() > 1;

                        $oldSelected = collect(
                            old(
                                "answers.{$question->id}",
                                []
                            )
                        )
                            ->map(fn ($id) => (int) $id)
                            ->all();
                    @endphp

                    <fieldset class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
                        <legend class="sr-only">
                            Question {{ $questionIndex + 1 }}
                        </legend>

                        <div class="flex gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-50 font-extrabold text-blue-700">
                                {{ $questionIndex + 1 }}
                            </span>

                            <div class="min-w-0 flex-1">
                                <h2 class="text-lg font-extrabold leading-7 text-slate-950 md:text-xl">
                                    {{ $question->question }}
                                </h2>

                                <p class="mt-2 text-sm font-semibold text-slate-500">
                                    @if($multipleCorrect)
                                        Select all answers that apply.
                                    @else
                                        Select one answer.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            @foreach($question->answers as $answer)
                                <label class="group flex cursor-pointer items-start gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-blue-400 hover:bg-blue-50/50">
                                    <input
                                        type="{{ $multipleCorrect ? 'checkbox' : 'radio' }}"
                                        name="answers[{{ $question->id }}][]"
                                        value="{{ $answer->id }}"
                                        @checked(
                                            in_array(
                                                $answer->id,
                                                $oldSelected,
                                                true
                                            )
                                        )
                                        class="mt-1 h-5 w-5 border-slate-300 text-blue-700 focus:ring-blue-600"
                                    >

                                    <span class="font-semibold leading-6 text-slate-800">
                                        {{ $answer->answer }}
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        @error("answers.{$question->id}")
                            <p class="mt-4 text-sm font-semibold text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </fieldset>
                @endforeach
            </div>

            <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:flex md:items-center md:justify-between md:gap-6">
                <div>
                    <p class="font-extrabold text-slate-950">
                        Ready to submit?
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        Make sure you have answered every question.
                    </p>
                </div>

                <button
                    type="submit"
                    class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-[rgb(var(--brand))] px-7 py-4 text-lg font-extrabold text-white transition hover:bg-[rgb(var(--brand-dark))] md:mt-0 md:w-auto"
                >
                    Submit quiz
                </button>
            </div>
        </form>
    </div>
</section>

@endsection