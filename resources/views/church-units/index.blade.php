@extends('layouts.site')

@section('content')
<section class="relative overflow-hidden bg-[rgb(var(--navy))] text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/80 via-slate-950/85 to-black"></div>
    <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-blue-500/15 blur-3xl"></div>

    <div class="relative mx-auto max-w-[1400px] px-4 py-20 md:py-28">
        <div class="max-w-4xl">
            <p class="text-sm font-extrabold uppercase tracking-[0.22em] text-blue-300">Get involved</p>
            <h1 class="mt-4 text-4xl font-extrabold leading-tight md:text-6xl">Find your place to serve</h1>
            <p class="mt-6 max-w-3xl text-lg leading-relaxed text-white/75 md:text-xl">
                Explore the ministries and teams that help Fresh Fountain serve people, build community and share the love of Christ.
            </p>
        </div>
    </div>
</section>

<section class="bg-gradient-to-b from-white to-slate-50">
    <div class="mx-auto max-w-[1400px] px-4 py-14 md:py-20">
        @if($units->isEmpty())
            <div class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                <h2 class="text-2xl font-extrabold text-slate-900">No church units available</h2>
                <p class="mt-2 text-slate-600">Please check again soon.</p>
            </div>
        @else
            <div class="grid gap-8 md:grid-cols-2 xl:grid-cols-3">
                @foreach($units as $unit)
                    <a href="{{ route('church-units.show', $unit->slug) }}"
                       class="group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_14px_40px_rgba(15,23,42,0.08)] transition duration-300 hover:-translate-y-1.5 hover:border-blue-200 hover:shadow-[0_24px_60px_rgba(15,23,42,0.15)] focus:outline-none focus:ring-4 focus:ring-blue-100">
                        <div class="relative h-56 overflow-hidden bg-gradient-to-br from-blue-800 to-slate-950">
                            @if($unit->feature_image)
                                <img src="{{ asset('storage/' . $unit->feature_image) }}" alt="{{ $unit->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-slate-950/10 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-6">
                                @if($unit->alias && $unit->alias !== $unit->name)
                                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-blue-200">{{ $unit->alias }}</p>
                                @else
                                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-blue-200">Church Unit</p>
                                @endif
                                <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-white">{{ $unit->name }}</h2>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col p-7">
                            <p class="flex-grow leading-relaxed text-slate-600">
                                {{ $unit->short_description ?: \Illuminate\Support\Str::limit($unit->description ?: 'Discover this ministry and find out how you can serve and grow with the team.', 180) }}
                            </p>

                            <div class="mt-7 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Meeting</p>
                                    <p class="mt-2 font-bold text-slate-900">{{ $unit->meeting_day ?: 'To be confirmed' }}</p>
                                    @if($unit->meeting_time)<p class="mt-1 text-sm text-slate-600">{{ $unit->meeting_time }}</p>@endif
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Location</p>
                                    <p class="mt-2 font-bold text-slate-900">{{ $unit->meeting_location ?: 'To be confirmed' }}</p>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap items-center gap-3 text-sm font-bold">
                                <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-blue-800">👥 {{ $unit->members_count }} {{ \Illuminate\Support\Str::plural('member', $unit->members_count) }}</span>
                                <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-amber-800">★ {{ $unit->leaders_count }} {{ \Illuminate\Support\Str::plural('leader', $unit->leaders_count) }}</span>
                            </div>

                            <div class="mt-7 flex items-center justify-between border-t border-slate-100 pt-5 font-extrabold text-blue-700">
                                <span>Explore this unit</span>
                                <span class="transition-transform duration-300 group-hover:translate-x-1">→</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="bg-[rgb(var(--navy))] text-white">
    <div class="mx-auto max-w-[1400px] px-4 py-14">
        <div class="flex flex-col gap-6 rounded-3xl border border-white/10 bg-white/5 p-8 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-extrabold md:text-3xl">Not sure which unit is right for you?</h2>
                <p class="mt-3 max-w-2xl text-white/70">Speak with a church leader and we will help you find a place where your gifts, interests and availability can flourish.</p>
            </div>
            <a href="/contact" class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-white px-6 py-3 font-extrabold text-slate-950 transition hover:bg-slate-100">Speak to a leader →</a>
        </div>
    </div>
</section>
@endsection
