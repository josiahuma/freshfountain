@extends('layouts.site')

@section('content')
@php
    $activeLeaders = $unit->leaders
        ->where('is_active', true)
        ->values();
@endphp

<section class="relative overflow-hidden bg-[rgb(var(--navy))] text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/70 via-slate-950/70 to-black"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 py-16 md:py-24">
        <a
            href="{{ route('church-units.index') }}"
            class="inline-flex items-center gap-2 font-extrabold text-blue-300 transition hover:text-white"
        >
            ← Back to church units
        </a>

        <div class="mt-10 grid gap-10 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-8">
                @if($unit->alias && $unit->alias !== $unit->name)
                    <p class="text-sm font-extrabold uppercase tracking-[0.22em] text-blue-300">
                        {{ $unit->alias }}
                    </p>
                @else
                    <p class="text-sm font-extrabold uppercase tracking-[0.22em] text-blue-300">
                        Church Unit
                    </p>
                @endif

                <h1 class="mt-4 text-4xl font-extrabold leading-tight md:text-6xl">
                    {{ $unit->name }}
                </h1>

                <p class="mt-6 max-w-3xl text-lg leading-relaxed text-white/75 md:text-xl">
                    {{ $unit->description ?: 'Discover this ministry and find out how you can serve, grow and make a meaningful contribution.' }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm font-bold">
                        👥 {{ $unit->members_count }}
                        {{ \Illuminate\Support\Str::plural('member', $unit->members_count) }}
                    </span>

                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm font-bold">
                        ★ {{ $activeLeaders->count() }}
                        {{ \Illuminate\Support\Str::plural('leader', $activeLeaders->count()) }}
                    </span>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="rounded-3xl border border-white/10 bg-white/10 p-7 backdrop-blur">
                    <h2 class="text-xl font-extrabold">
                        Ready to serve?
                    </h2>

                    <p class="mt-3 leading-relaxed text-white/70">
                        Submit a short request and one of the unit leaders will contact you.
                    </p>

                    <a
                        href="{{ route('church-units.join', $unit->slug) }}"
                        class="mt-6 inline-flex w-full items-center justify-center rounded-2xl
                               bg-[rgb(var(--brand))] px-6 py-3 font-extrabold text-white
                               transition hover:bg-[rgb(var(--brand-dark))]"
                    >
                        Join {{ $unit->name }} →
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-gradient-to-b from-white to-slate-50">
    <div class="max-w-[1400px] mx-auto px-4 py-14 md:py-20">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-8">
                <div class="rounded-3xl border border-slate-200 bg-white p-7
                            shadow-[0_14px_40px_rgba(15,23,42,0.08)] md:p-10">
                    <p class="text-sm font-extrabold uppercase tracking-[0.18em] text-blue-600">
                        About this unit
                    </p>

                    <h2 class="mt-3 text-3xl font-extrabold text-slate-950">
                        Serve, grow and belong
                    </h2>

                    <div class="mt-6 whitespace-pre-line text-lg leading-8 text-slate-600">
                        {{ $unit->description ?: 'More information about this ministry will be available shortly.' }}
                    </div>
                </div>

                <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-7
                            shadow-[0_14px_40px_rgba(15,23,42,0.08)] md:p-10">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-extrabold uppercase tracking-[0.18em] text-blue-600">
                                Leadership
                            </p>

                            <h2 class="mt-3 text-3xl font-extrabold text-slate-950">
                                Meet the unit leaders
                            </h2>
                        </div>

                        <p class="text-sm text-slate-500">
                            {{ $activeLeaders->count() }}
                            active {{ \Illuminate\Support\Str::plural('leader', $activeLeaders->count()) }}
                        </p>
                    </div>

                    @if($activeLeaders->isEmpty())
                        <div class="mt-8 rounded-2xl bg-slate-50 p-6 text-slate-600">
                            Leadership information will be available shortly.
                        </div>
                    @else
                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            @foreach($activeLeaders as $leader)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-100 font-extrabold text-blue-800">
                                            {{ \Illuminate\Support\Str::upper(
                                                \Illuminate\Support\Str::substr($leader->first_name, 0, 1)
                                            ) }}
                                        </div>

                                        <div>
                                            <h3 class="font-extrabold text-slate-950">
                                                {{ $leader->display_name }}
                                            </h3>

                                            <p class="mt-1 text-sm font-semibold text-blue-700">
                                                {{ $leader->leadership_role }}
                                            </p>

                                            @if($leader->email)
                                                <p class="mt-2 text-sm text-slate-500">
                                                    {{ $leader->email }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <aside class="lg:col-span-4">
                <div class="space-y-6 lg:sticky lg:top-28">
                    <div class="rounded-3xl border border-slate-200 bg-white p-7
                                shadow-[0_14px_40px_rgba(15,23,42,0.08)]">
                        <h2 class="text-xl font-extrabold text-slate-950">
                            Meeting information
                        </h2>

                        <dl class="mt-6 space-y-5">
                            <div>
                                <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                    Day
                                </dt>
                                <dd class="mt-1 font-bold text-slate-950">
                                    {{ $unit->meeting_day ?: 'To be confirmed' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                    Time
                                </dt>
                                <dd class="mt-1 font-bold text-slate-950">
                                    {{ $unit->meeting_time ?: 'To be confirmed' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                    Location
                                </dt>
                                <dd class="mt-1 font-bold text-slate-950">
                                    {{ $unit->meeting_location ?: 'To be confirmed' }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-3xl bg-[rgb(var(--navy))] p-7 text-white">
                        <h2 class="text-xl font-extrabold">
                            Join this team
                        </h2>

                        <p class="mt-3 leading-relaxed text-white/70">
                            Tell us how to contact you and the leadership team will follow up.
                        </p>

                        <a
                            href="{{ route('church-units.join', $unit->slug) }}"
                            class="mt-6 inline-flex w-full items-center justify-center rounded-2xl
                                   bg-[rgb(var(--brand))] px-6 py-3 font-extrabold text-white
                                   transition hover:bg-[rgb(var(--brand-dark))]"
                        >
                            Request to join
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection