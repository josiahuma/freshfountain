@extends('layouts.site')

@section('content')
<section class="relative overflow-hidden bg-[rgb(var(--navy))] text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/70 via-slate-950/60 to-black"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 py-20 md:py-28">
        <div class="max-w-4xl">
            <p class="text-sm font-extrabold uppercase tracking-[0.22em] text-blue-300">
                Get involved
            </p>

            <h1 class="mt-4 text-4xl md:text-6xl font-extrabold leading-tight">
                Explore Our Church Units
            </h1>

            <p class="mt-6 max-w-3xl text-lg md:text-xl leading-relaxed text-white/75">
                Whether your passion is worship, media, hospitality, prayer,
                children’s ministry or outreach, there is a place for you to
                serve, grow and make a meaningful difference.
            </p>
        </div>
    </div>
</section>

<section class="bg-gradient-to-b from-white to-slate-50">
    <div class="max-w-[1400px] mx-auto px-4 py-14 md:py-20">

        @if($units->isEmpty())
            <div class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-2xl">
                    👥
                </div>

                <h2 class="mt-5 text-2xl font-extrabold text-slate-900">
                    No church units available
                </h2>

                <p class="mt-2 text-slate-600">
                    Please check again soon.
                </p>
            </div>
        @else
            <div class="grid gap-8 md:grid-cols-2 xl:grid-cols-3">
                @foreach($units as $unit)
                    <article
                        class="group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white
                               shadow-[0_14px_40px_rgba(15,23,42,0.08)]
                               transition duration-300 hover:-translate-y-1
                               hover:shadow-[0_22px_55px_rgba(15,23,42,0.14)]"
                    >
                        <div class="h-2 bg-gradient-to-r from-blue-700 via-blue-500 to-cyan-400"></div>

                        <div class="flex flex-1 flex-col p-7 md:p-8">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    @if($unit->alias && $unit->alias !== $unit->name)
                                        <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-blue-600">
                                            {{ $unit->alias }}
                                        </p>
                                    @endif

                                    <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950">
                                        {{ $unit->name }}
                                    </h2>
                                </div>

                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-xl">
                                    ✦
                                </div>
                            </div>

                            <p class="mt-5 flex-grow leading-relaxed text-slate-600">
                                {{ \Illuminate\Support\Str::limit(
                                    $unit->description ?: 'Discover this ministry and find out how you can serve and grow with the team.',
                                    180
                                ) }}
                            </p>

                            <div class="mt-7 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        Meeting
                                    </p>

                                    <p class="mt-2 font-bold text-slate-900">
                                        {{ $unit->meeting_day ?: 'To be confirmed' }}
                                    </p>

                                    @if($unit->meeting_time)
                                        <p class="mt-1 text-sm text-slate-600">
                                            {{ $unit->meeting_time }}
                                        </p>
                                    @endif
                                </div>

                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                        Location
                                    </p>

                                    <p class="mt-2 font-bold text-slate-900">
                                        {{ $unit->meeting_location ?: 'To be confirmed' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap items-center gap-3 text-sm font-bold">
                                <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-blue-800">
                                    <span>👥</span>
                                    {{ $unit->members_count }}
                                    {{ \Illuminate\Support\Str::plural('member', $unit->members_count) }}
                                </span>

                                <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-amber-800">
                                    <span>★</span>
                                    {{ $unit->leaders_count }}
                                    {{ \Illuminate\Support\Str::plural('leader', $unit->leaders_count) }}
                                </span>
                            </div>

                            <div class="mt-8 grid gap-3 sm:grid-cols-2">
                                <a
                                    href="{{ route('church-units.show', $unit->slug) }}"
                                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200
                                           bg-white px-5 py-3 font-extrabold text-slate-900
                                           transition hover:bg-slate-50"
                                >
                                    Learn more
                                </a>

                                <a
                                    href="{{ route('church-units.join', $unit->slug) }}"
                                    class="inline-flex items-center justify-center rounded-2xl
                                           bg-[rgb(var(--brand))] px-5 py-3 font-extrabold text-white
                                           shadow-sm transition hover:bg-[rgb(var(--brand-dark))]"
                                >
                                    Join this unit
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="bg-[rgb(var(--navy))] text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-14">
        <div class="flex flex-col gap-6 rounded-3xl border border-white/10 bg-white/5 p-8 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold">
                    Not sure which unit is right for you?
                </h2>

                <p class="mt-3 max-w-2xl text-white/70">
                    Speak with a church leader and we will help you find a place
                    where your gifts, interests and availability can flourish.
                </p>
            </div>

            <a
                href="/contact"
                class="inline-flex shrink-0 items-center justify-center rounded-2xl
                       bg-white px-6 py-3 font-extrabold text-slate-950
                       transition hover:bg-slate-100"
            >
                Speak to a leader →
            </a>
        </div>
    </div>
</section>
@endsection