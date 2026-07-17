@extends('layouts.site')

@section('content')
@php
    $input = 'mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3
              text-slate-900 placeholder-slate-400 shadow-sm outline-none
              focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20';
@endphp

<section class="relative overflow-hidden bg-[rgb(var(--navy))] text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/70 via-slate-950/70 to-black"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 py-16 md:py-20">
        <a
            href="{{ route('church-units.show', $churchUnit->slug) }}"
            class="inline-flex items-center gap-2 font-extrabold text-blue-300 transition hover:text-white"
        >
            ← Back to {{ $churchUnit->name }}
        </a>

        <p class="mt-10 text-sm font-extrabold uppercase tracking-[0.22em] text-blue-300">
            Join a church unit
        </p>

        <h1 class="mt-4 text-4xl font-extrabold leading-tight md:text-6xl">
            Join {{ $churchUnit->name }}
        </h1>

        <p class="mt-5 max-w-3xl text-lg leading-relaxed text-white/75">
            Complete this short form and one of the unit leaders will contact you.
        </p>
    </div>
</section>

<section class="bg-gradient-to-b from-white to-slate-50">
    <div class="max-w-[1400px] mx-auto px-4 py-14 md:py-20">
        @if($errors->any())
            <div class="mb-8 rounded-3xl border border-red-200 bg-red-50 p-6 text-red-900">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-100 font-extrabold">
                        !
                    </div>

                    <div>
                        <p class="font-extrabold">
                            Please check the form.
                        </p>

                        <ul class="mt-2 space-y-1 text-sm text-red-700">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-8">
                <form
                    method="POST"
                    action="{{ route('church-units.store', $churchUnit->slug) }}"
                    class="rounded-3xl border border-slate-200 bg-white p-7
                           shadow-[0_14px_40px_rgba(15,23,42,0.10)] md:p-10"
                >
                    @csrf

                    <div>
                        <p class="text-sm font-extrabold uppercase tracking-[0.18em] text-blue-600">
                            Your details
                        </p>

                        <h2 class="mt-3 text-3xl font-extrabold text-slate-950">
                            Tell us how to contact you
                        </h2>

                        <p class="mt-3 text-slate-600">
                            We will use these details only to process and follow up your unit request.
                        </p>
                    </div>

                    <div class="mt-8 grid gap-6 md:grid-cols-2">
                        <label class="block">
                            <span class="font-extrabold text-slate-900">
                                First name <span class="text-red-600">*</span>
                            </span>

                            <input
                                type="text"
                                name="first_name"
                                value="{{ old('first_name') }}"
                                required
                                autocomplete="given-name"
                                class="{{ $input }}"
                            >

                            @error('first_name')
                                <span class="mt-2 block text-sm font-semibold text-red-600">
                                    {{ $message }}
                                </span>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="font-extrabold text-slate-900">
                                Last name
                            </span>

                            <input
                                type="text"
                                name="last_name"
                                value="{{ old('last_name') }}"
                                autocomplete="family-name"
                                class="{{ $input }}"
                            >

                            @error('last_name')
                                <span class="mt-2 block text-sm font-semibold text-red-600">
                                    {{ $message }}
                                </span>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="font-extrabold text-slate-900">
                                Email address <span class="text-red-600">*</span>
                            </span>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                class="{{ $input }}"
                            >

                            @error('email')
                                <span class="mt-2 block text-sm font-semibold text-red-600">
                                    {{ $message }}
                                </span>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="font-extrabold text-slate-900">
                                Mobile number <span class="text-red-600">*</span>
                            </span>

                            <input
                                type="tel"
                                name="mobile_number"
                                value="{{ old('mobile_number') }}"
                                required
                                autocomplete="tel"
                                class="{{ $input }}"
                            >

                            @error('mobile_number')
                                <span class="mt-2 block text-sm font-semibold text-red-600">
                                    {{ $message }}
                                </span>
                            @enderror
                        </label>

                        <label class="block md:col-span-2">
                            <span class="font-extrabold text-slate-900">
                                Message
                            </span>

                            <span class="ml-1 text-sm font-normal text-slate-500">
                                Optional
                            </span>

                            <textarea
                                name="message"
                                rows="6"
                                placeholder="Tell us briefly why you would like to join this unit or how you would like to serve."
                                class="{{ $input }}"
                            >{{ old('message') }}</textarea>

                            @error('message')
                                <span class="mt-2 block text-sm font-semibold text-red-600">
                                    {{ $message }}
                                </span>
                            @enderror
                        </label>
                    </div>

                    <div class="mt-8 rounded-2xl border border-blue-100 bg-blue-50 p-5">
                        <p class="font-extrabold text-blue-950">
                            What happens next?
                        </p>

                        <p class="mt-2 text-sm leading-relaxed text-blue-900/75">
                            Your request will be sent securely to the leadership team for
                            {{ $churchUnit->name }}. One of the leaders will contact you using
                            the information provided.
                        </p>
                    </div>

                    <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
                        <a
                            href="{{ route('church-units.show', $churchUnit->slug) }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200
                                   bg-white px-6 py-3 font-extrabold text-slate-900
                                   transition hover:bg-slate-50"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-2xl
                                   bg-[rgb(var(--brand))] px-7 py-3 font-extrabold text-white
                                   shadow-sm transition hover:bg-[rgb(var(--brand-dark))]"
                        >
                            Submit request →
                        </button>
                    </div>
                </form>
            </div>

            <aside class="lg:col-span-4">
                <div class="space-y-6 lg:sticky lg:top-28">
                    <div class="rounded-3xl border border-slate-200 bg-white p-7
                                shadow-[0_14px_40px_rgba(15,23,42,0.08)]">
                        <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-blue-600">
                            Selected unit
                        </p>

                        <h2 class="mt-3 text-2xl font-extrabold text-slate-950">
                            {{ $churchUnit->name }}
                        </h2>

                        @if($churchUnit->alias && $churchUnit->alias !== $churchUnit->name)
                            <p class="mt-1 font-semibold text-blue-700">
                                {{ $churchUnit->alias }}
                            </p>
                        @endif

                        <p class="mt-5 leading-relaxed text-slate-600">
                            {{ \Illuminate\Support\Str::limit(
                                $churchUnit->description ?: 'Become part of this team and serve alongside others.',
                                260
                            ) }}
                        </p>
                    </div>

                    <div class="rounded-3xl bg-[rgb(var(--navy))] p-7 text-white">
                        <h3 class="text-xl font-extrabold">
                            Meeting information
                        </h3>

                        <dl class="mt-6 space-y-5">
                            <div>
                                <dt class="text-xs font-extrabold uppercase tracking-wide text-white/50">
                                    Day
                                </dt>
                                <dd class="mt-1 font-bold">
                                    {{ $churchUnit->meeting_day ?: 'To be confirmed' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-extrabold uppercase tracking-wide text-white/50">
                                    Time
                                </dt>
                                <dd class="mt-1 font-bold">
                                    {{ $churchUnit->meeting_time ?: 'To be confirmed' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-extrabold uppercase tracking-wide text-white/50">
                                    Location
                                </dt>
                                <dd class="mt-1 font-bold">
                                    {{ $churchUnit->meeting_location ?: 'To be confirmed' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection