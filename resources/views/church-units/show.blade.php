@extends('layouts.site')

@section('content')
@php
    $activeLeaders = $unit->leaders->where('is_active', true)->values();
@endphp

<section class="relative min-h-[560px] overflow-hidden bg-[rgb(var(--navy))] text-white">
    @if($unit->feature_image)
        <div class="absolute inset-0">
            <img src="{{ asset('storage/' . $unit->feature_image) }}" alt="{{ $unit->name }}" class="h-full w-full object-cover">
        </div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/78 to-slate-950/35"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-transparent to-slate-950/20"></div>

    <div class="relative mx-auto flex min-h-[560px] max-w-[1400px] flex-col justify-between px-4 py-12 md:py-16">
        <a href="{{ route('church-units.index') }}" class="inline-flex w-fit items-center gap-2 font-extrabold text-blue-200 transition hover:text-white">← Back to church units</a>

        <div class="max-w-4xl py-12">
            <p class="text-sm font-extrabold uppercase tracking-[0.22em] text-blue-300">{{ ($unit->alias && $unit->alias !== $unit->name) ? $unit->alias : 'Church Unit' }}</p>
            <h1 class="mt-4 text-4xl font-extrabold leading-tight md:text-6xl lg:text-7xl">{{ $unit->name }}</h1>
            <p class="mt-6 max-w-3xl text-lg leading-relaxed text-white/80 md:text-xl">
                {{ $unit->short_description ?: 'Discover this ministry and find out how you can serve, grow and make a meaningful contribution.' }}
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-bold backdrop-blur">👥 {{ $unit->members_count }} {{ \Illuminate\Support\Str::plural('member', $unit->members_count) }}</span>
                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-bold backdrop-blur">★ {{ $activeLeaders->count() }} {{ \Illuminate\Support\Str::plural('leader', $activeLeaders->count()) }}</span>
            </div>
        </div>
    </div>
</section>

<section class="bg-gradient-to-b from-white to-slate-50">
    <div class="mx-auto max-w-[1400px] px-4 py-14 md:py-20">
        <div class="grid gap-10 lg:grid-cols-12">
            <main class="lg:col-span-8">
                <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-[0_14px_40px_rgba(15,23,42,0.08)] md:p-10">
                    <p class="text-sm font-extrabold uppercase tracking-[0.18em] text-blue-600">About {{ $unit->name }}</p>
                    <h2 class="mt-3 text-3xl font-extrabold text-slate-950">Serve, grow and belong</h2>
                    <div class="mt-6 whitespace-pre-line text-lg leading-8 text-slate-600">{{ $unit->description ?: 'More information about this ministry will be available shortly.' }}</div>
                </div>

                <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-7 shadow-[0_14px_40px_rgba(15,23,42,0.08)] md:p-10">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-extrabold uppercase tracking-[0.18em] text-blue-600">Leadership</p>
                            <h2 class="mt-3 text-3xl font-extrabold text-slate-950">Meet the unit leaders</h2>
                        </div>
                        <p class="text-sm text-slate-500">{{ $activeLeaders->count() }} active {{ \Illuminate\Support\Str::plural('leader', $activeLeaders->count()) }}</p>
                    </div>

                    @if($activeLeaders->isEmpty())
                        <div class="mt-8 rounded-2xl bg-slate-50 p-6 text-slate-600">Leadership information will be available shortly.</div>
                    @else
                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            @foreach($activeLeaders as $leader)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-100 font-extrabold text-blue-800">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($leader->first_name, 0, 1)) }}</div>
                                        <div>
                                            <h3 class="font-extrabold text-slate-950">{{ $leader->display_name }}</h3>
                                            <p class="mt-1 text-sm font-semibold text-blue-700">{{ $leader->leadership_role }}</p>
                                            @if($leader->email)<p class="mt-2 text-sm text-slate-500">{{ $leader->email }}</p>@endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </main>

            <aside class="lg:col-span-4">
                <div class="space-y-6 lg:sticky lg:top-28">
                    <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-[0_14px_40px_rgba(15,23,42,0.08)]">
                        <p class="text-sm font-extrabold uppercase tracking-[0.18em] text-blue-600">At a glance</p>
                        <h2 class="mt-2 text-2xl font-extrabold text-slate-950">Meeting information</h2>
                        <dl class="mt-6 divide-y divide-slate-100">
                            <div class="py-4 first:pt-0"><dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Day</dt><dd class="mt-1 font-bold text-slate-950">{{ $unit->meeting_day ?: 'To be confirmed' }}</dd></div>
                            <div class="py-4"><dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Time</dt><dd class="mt-1 font-bold text-slate-950">{{ $unit->meeting_time ?: 'To be confirmed' }}</dd></div>
                            <div class="py-4 pb-0"><dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Location</dt><dd class="mt-1 font-bold text-slate-950">{{ $unit->meeting_location ?: 'To be confirmed' }}</dd></div>
                        </dl>
                    </div>

                    <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 to-blue-950 p-7 text-white shadow-[0_18px_45px_rgba(30,64,175,0.25)]">
                        <p class="text-sm font-extrabold uppercase tracking-[0.18em] text-blue-200">Get involved</p>
                        <h2 class="mt-2 text-2xl font-extrabold">Join {{ $unit->name }}</h2>
                        <p class="mt-3 leading-relaxed text-white/75">Tell us how to contact you and the leadership team will follow up with you.</p>
                        <a href="{{ route('church-units.join', $unit->slug) }}" class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-white px-6 py-3 font-extrabold text-blue-900 transition hover:bg-blue-50">Request to join →</a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
