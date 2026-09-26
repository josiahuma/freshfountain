@extends('layouts.site')

@section('title', $page->seo_title ?: 'Rooted Generation | Fresh Fountain')
@section('meta_description', $page->seo_description ?: 'Rooted Generation is Fresh Fountain’s youth ministry, helping young people become rooted in Christ, grounded in faith, growing in purpose and fruitful in their world.')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $rooted = data_get($page->sections, 'rooted', []);
    $heroUpload = data_get($rooted, 'hero_image');
    $thriveUpload = data_get($rooted, 'thrive_image');

    $connectImage = $heroUpload ? Storage::url($heroUpload) : asset('images/rooted-generation/rooted-connect.jpg');
    $thriveImage = $thriveUpload ? Storage::url($thriveUpload) : asset('images/rooted-generation/thrive-summit.jpg');

    $objectives = data_get($rooted, 'objectives');

    $heroPhotos = [
        asset('images/rooted-generation/hero/community-1.jpg'),
        asset('images/rooted-generation/hero/community-2.jpg'),
        asset('images/rooted-generation/hero/community-3.jpg'),
        asset('images/rooted-generation/hero/community-4.jpg'),
    ];
@endphp

{{-- =========================================================
    ROOTED GENERATION HERO
    Real HTML/Tailwind content + four separate photo assets.
========================================================= --}}
<section class="relative isolate overflow-hidden bg-[#031126] text-white">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -left-32 top-0 h-[520px] w-[520px] rounded-full bg-blue-600/20 blur-3xl"></div>
        <div class="absolute right-[-10rem] top-[-8rem] h-[560px] w-[560px] rounded-full bg-fuchsia-600/20 blur-3xl"></div>
        <div class="absolute bottom-[-15rem] left-[38%] h-[500px] w-[500px] rounded-full bg-cyan-500/10 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.06]"
             style="background-image:radial-gradient(circle at 1px 1px,white 1px,transparent 0);background-size:24px 24px;"></div>
    </div>

    <div class="relative mx-auto max-w-[1500px] px-4 py-12 sm:px-6 md:py-16 lg:px-8 lg:py-20">
        <div class="grid gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">

            {{-- REAL WEB CONTENT --}}
            <div class="relative z-20 max-w-2xl">
                <div class="mb-8 flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}"
                         alt="Fresh Fountain"
                         class="h-12 w-auto brightness-0 invert"
                         onerror="this.style.display='none'">
                </div>

                <p class="text-sm font-extrabold uppercase tracking-[0.24em] text-blue-300">
                    Fresh Fountain Youth Ministry
                </p>

                <h1 class="mt-4 text-5xl font-black leading-[0.9] tracking-[-0.045em] sm:text-6xl md:text-7xl xl:text-[6.2rem]">
                    <span class="block text-white">Rooted</span>
                    <span class="block bg-gradient-to-r from-cyan-400 via-blue-400 to-fuchsia-500 bg-clip-text text-transparent">
                        Generation
                    </span>
                </h1>

                <div class="mt-5 h-2 w-56 -rotate-1 rounded-full bg-gradient-to-r from-amber-400 to-orange-500"></div>

                <h2 class="mt-7 text-2xl font-black leading-tight text-white sm:text-3xl">
                    Rooted. Grounded. Growing. <span class="text-amber-400">Fruitful.</span>
                </h2>

                <p class="mt-5 max-w-xl text-base leading-7 text-slate-200 sm:text-lg sm:leading-8">
                    A Christ-centred community where young people can belong, grow in faith,
                    discover purpose and confidently influence their world for Christ.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="#connect"
                       class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-7 py-4 text-base font-extrabold text-white shadow-[0_18px_45px_rgba(37,99,235,.35)] transition hover:-translate-y-0.5 hover:bg-blue-500">
                        Join the community <span class="ml-2">→</span>
                    </a>

                    <a href="#thrive"
                       class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-7 py-4 text-base font-extrabold text-white backdrop-blur transition hover:bg-white/15">
                        Explore Thrive Summit
                    </a>
                </div>
            </div>

            {{-- FOUR REAL PHOTO ELEMENTS --}}
            <div class="relative min-h-[480px] sm:min-h-[570px] lg:min-h-[620px]">
                <div class="absolute left-[3%] top-[7%] z-10 w-[47%] rotate-[-2deg] overflow-hidden rounded-[2rem] border-4 border-white/90 shadow-2xl">
                    <img src="{{ $heroPhotos[0] }}" alt="Rooted Generation community" class="aspect-[4/5] w-full object-cover">
                </div>

                <div class="absolute right-[2%] top-[0%] z-20 w-[48%] rotate-[2deg] overflow-hidden rounded-[2rem] border-4 border-white/90 shadow-2xl">
                    <img src="{{ $heroPhotos[1] }}" alt="Young person at Rooted Generation" class="aspect-[4/5] w-full object-cover">
                </div>

                <div class="absolute right-[-1%] top-[43%] z-10 w-[42%] rotate-[2deg] overflow-hidden rounded-[2rem] border-4 border-white/90 shadow-2xl">
                    <img src="{{ $heroPhotos[2] }}" alt="Rooted Generation gathering" class="aspect-[4/3] w-full object-cover">
                </div>

                <div class="absolute bottom-[1%] left-[22%] z-30 w-[55%] rotate-[-1deg] overflow-hidden rounded-[2rem] border-4 border-white/90 shadow-2xl">
                    <img src="{{ $heroPhotos[3] }}" alt="Rooted Generation friends" class="aspect-[16/9] w-full object-cover">
                </div>

                {{-- decorative web elements --}}
                <div class="absolute left-[0%] top-[43%] z-40 text-6xl font-black text-amber-400 rotate-[-15deg]">〽</div>
                <div class="absolute right-[4%] top-[35%] z-40 h-2 w-28 rotate-[-18deg] rounded-full bg-fuchsia-500"></div>
                <div class="absolute right-[7%] top-[38%] z-40 h-2 w-20 rotate-[-18deg] rounded-full bg-fuchsia-400"></div>
                <div class="absolute bottom-[10%] left-[9%] z-40 h-2 w-24 rotate-[-20deg] rounded-full bg-amber-400"></div>
            </div>
        </div>

        {{-- REAL PILLAR STRIP --}}
        <div class="relative z-30 mt-10 grid overflow-hidden rounded-[2rem] border border-white/10 bg-white text-slate-950 shadow-2xl sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['📖', 'ROOTED', 'In Christ, Scripture and prayer.', 'text-blue-600'],
                ['♡', 'GROUNDED', 'In identity, love and belonging.', 'text-rose-600'],
                ['👥', 'GROWING', 'In wisdom, character and leadership.', 'text-fuchsia-600'],
                ['◎', 'FRUITFUL', 'Living with purpose and influencing the world for Christ.', 'text-amber-500'],
            ] as [$icon, $title, $text, $colour])
                <div class="flex gap-4 border-slate-100 p-6 sm:border-r last:border-r-0">
                    <div class="text-4xl font-black {{ $colour }}">{{ $icon }}</div>
                    <div>
                        <h3 class="text-sm font-black">{{ $title }}</h3>
                        <p class="mt-1 text-sm leading-5 text-slate-600">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="mx-auto max-w-[1250px] px-4 py-16 md:py-24">
        <div class="grid gap-12 lg:grid-cols-[.8fr_1.2fr] lg:items-start">
            <div>
                <p class="text-sm font-extrabold uppercase tracking-[0.2em] text-blue-600">Who we are</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 md:text-5xl">A generation deeply rooted in Christ.</h2>
                <p class="mt-5 text-lg leading-8 text-slate-600">Faith for real life. Community that feels like home. Wisdom for the journey. Purpose that reaches beyond Sunday.</p>
            </div>
            <div class="rounded-[32px] border border-slate-200 bg-slate-50 p-7 shadow-sm md:p-10">
                @if($objectives)
                    <div class="prose prose-slate prose-lg max-w-none prose-p:leading-8">{!! $objectives !!}</div>
                @else
                    <div class="space-y-5 text-lg leading-8 text-slate-700">
                        <p>Rooted Generation exists to help young people build a strong and lasting relationship with Christ, grounded in faith, Scripture, prayer and His love.</p>
                        <p>We create a safe and welcoming community where young people can discover their identity, develop Christ-like character and feel a sense of belonging.</p>
                        <p>We equip them with biblical and practical wisdom to navigate relationships, education, culture, purpose and everyday life.</p>
                        <p>We encourage young people to discover their God-given gifts, grow as leaders, worship God and confidently share their faith.</p>
                        <p class="font-bold text-slate-950">Ultimately, our goal is to raise a generation that is rooted, grounded, growing and fruitful, influencing their world for Christ.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<section id="connect" class="bg-slate-950 text-white">
    <div class="mx-auto grid max-w-[1400px] gap-10 px-4 py-16 md:py-24 lg:grid-cols-2 lg:items-center">
        <div class="overflow-hidden rounded-[34px] border border-white/10 bg-white/5 p-3 shadow-2xl">
            <img src="{{ $connectImage }}" alt="Rooted Generation Connect" class="w-full rounded-[26px] object-cover">
        </div>
        <div class="lg:pl-8">
            <p class="text-sm font-extrabold uppercase tracking-[0.2em] text-blue-300">Connect • Belong • Grow</p>
            <h2 class="mt-4 text-4xl font-black md:text-5xl">{{ data_get($rooted, 'connect_title', 'Rooted Generation Connect') }}</h2>
            <p class="mt-5 text-lg leading-8 text-slate-300">A regular space for young people to build friendships, worship, talk honestly about faith and life, and grow together.</p>
            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-white/10 p-5"><p class="text-xs font-extrabold uppercase tracking-wider text-blue-300">When</p><p class="mt-2 text-lg font-bold">{{ data_get($rooted, 'connect_schedule', 'Every second Sunday of the month') }}</p></div>
                <div class="rounded-2xl bg-white/10 p-5"><p class="text-xs font-extrabold uppercase tracking-wider text-blue-300">Time</p><p class="mt-2 text-lg font-bold">{{ data_get($rooted, 'connect_time', '2:00 PM') }}</p></div>
                <div class="rounded-2xl bg-white/10 p-5 sm:col-span-2"><p class="text-xs font-extrabold uppercase tracking-wider text-blue-300">Venue</p><p class="mt-2 text-lg font-bold">{{ data_get($rooted, 'connect_location', 'Fresh Fountain Centre, 7 Gregory Boulevard, NG7 6LB, Nottingham') }}</p></div>
            </div>
        </div>
    </div>
</section>

<section id="thrive" class="bg-gradient-to-b from-orange-50 to-white">
    <div class="mx-auto max-w-[1400px] px-4 py-16 md:py-24">
        <div class="grid gap-12 lg:grid-cols-[.9fr_1.1fr] lg:items-start">
            <div class="lg:sticky lg:top-28">
                <img src="{{ $thriveImage }}" alt="Rooted Generation Thrive Summit" class="w-full rounded-[32px] border border-orange-100 shadow-xl">
            </div>
            <div>
                <p class="text-sm font-extrabold uppercase tracking-[0.2em] text-orange-600">Featured Event</p>
                <h2 class="mt-3 text-4xl font-black tracking-tight text-slate-950 md:text-5xl">{{ data_get($rooted, 'summit_title', 'Thrive Summit') }}</h2>
                <p class="mt-5 text-lg leading-8 text-slate-600">{{ data_get($rooted, 'summit_intro', 'A practical and welcoming summit helping international students and young people navigate life in the UK with greater confidence, useful resources and a community to lean on.') }}</p>

                <div class="mt-7 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-orange-100 bg-white p-5"><p class="text-xs font-extrabold uppercase text-orange-600">Date</p><p class="mt-2 font-bold text-slate-950">{{ data_get($rooted, 'summit_date', 'Sunday, 11 October 2026') }}</p></div>
                    <div class="rounded-2xl border border-orange-100 bg-white p-5"><p class="text-xs font-extrabold uppercase text-orange-600">Time</p><p class="mt-2 font-bold text-slate-950">{{ data_get($rooted, 'summit_time', '2:00 PM') }}</p></div>
                    <div class="rounded-2xl border border-orange-100 bg-white p-5 sm:col-span-2"><p class="text-xs font-extrabold uppercase text-orange-600">Venue</p><p class="mt-2 font-bold text-slate-950">{{ data_get($rooted, 'summit_location', 'Fresh Fountain Centre, 7 Gregory Boulevard, Sherwood House, NG7 6LB') }}</p></div>
                </div>

                <h3 class="mt-10 text-2xl font-black text-slate-950">What we’ll explore</h3>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @foreach([
                        ['💼', 'Lifeline: Careers & Opportunities', 'Practical career advice, real-life experiences, guest speakers and ideas for your next step.'],
                        ['🇬🇧', 'Navigating Life in the UK', 'Relationships, support networks, mental wellbeing, faith and community.'],
                        ['🎓', 'Studies, Leisure & Everyday Life', 'Balance study with life, discover Nottingham, useful community resources and transport tips.'],
                        ['📚', 'Research & Assignments', 'Find reliable information, academic resources and practical support when you get stuck.'],
                        ['❓', 'Q&A: Ask Us Anything', 'Bring questions about study, careers, relationships, opportunities and life in the UK.'],
                        ['🤝', 'Connect & Build Your Tribe', 'Icebreakers, games and conversations designed to help meaningful friendships grow.'],
                    ] as [$icon, $title, $text])
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="text-2xl">{{ $icon }}</div>
                            <h4 class="mt-3 font-black text-slate-950">{{ $title }}</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $text }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 rounded-3xl bg-slate-950 p-7 text-white">
                    <p class="text-lg font-bold">Come ready to learn. Come ready to connect. Come find your tribe. ❤️</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-blue-700 text-white">
    <div class="mx-auto flex max-w-[1200px] flex-col gap-8 px-4 py-14 md:flex-row md:items-center md:justify-between">

        <div>
            <p class="text-sm font-extrabold uppercase tracking-[0.2em] text-blue-200">
                Rooted Generation
            </p>

            <h2 class="mt-2 text-3xl font-black">
                There’s a place for you here.
            </h2>

            <p class="mt-2 max-w-2xl text-blue-100">
                Come connect, ask questions, grow in Christ and build genuine friendships.
            </p>

            <p class="mt-4 text-sm font-semibold text-blue-200">
                Connect with Rooted Generation online or get in touch with the team.
            </p>
        </div>

        <div class="flex shrink-0 flex-wrap gap-3">

            {{-- Email --}}
            <a
                href="mailto:rootedgeneration@freshfountain.org"
                class="inline-flex items-center justify-center gap-2 rounded-2xl
                       bg-white px-6 py-3.5 font-extrabold text-blue-800
                       transition hover:-translate-y-0.5 hover:bg-blue-50"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 6.75A2.25 2.25 0 0 1 5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 17.25V6.75Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m3.75 6 7.2 5.4a1.75 1.75 0 0 0 2.1 0L20.25 6"/>
                </svg>

                Email us
            </a>

            {{-- WhatsApp --}}
            <a
                href="https://wa.me/447840222374"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-2 rounded-2xl
                       bg-green-500 px-6 py-3.5 font-extrabold text-white
                       transition hover:-translate-y-0.5 hover:bg-green-400"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.04 2C6.52 2 2.03 6.49 2.03 12c0 1.76.46 3.48 1.33 4.99L2 22l5.13-1.35A9.96 9.96 0 0 0 12.04 22C17.56 22 22.05 17.51 22.05 12S17.56 2 12.04 2Zm5.84 14.12c-.25.7-1.47 1.34-2.02 1.43-.52.1-1.18.14-1.9-.09-.44-.14-1-.33-1.72-.64-3.03-1.31-5-4.36-5.15-4.56-.15-.2-1.23-1.64-1.23-3.13 0-1.49.78-2.22 1.06-2.52.28-.3.61-.37.81-.37h.59c.19 0 .44-.07.69.53.25.6.85 2.07.92 2.22.08.15.13.33.03.53-.1.2-.15.33-.3.51-.15.18-.32.4-.45.54-.15.15-.31.31-.13.61.18.3.8 1.32 1.72 2.14 1.18 1.05 2.18 1.38 2.48 1.53.3.15.48.13.66-.08.18-.2.76-.89.96-1.19.2-.3.4-.25.68-.15.28.1 1.77.84 2.07.99.3.15.5.23.58.35.07.13.07.73-.18 1.43Z"/>
                </svg>

                WhatsApp us
            </a>

            {{-- Instagram --}}
            <a
                href="https://www.instagram.com/rootedgeneration_ff?stkn=MWV1emMzNHB0bWoxYg=="
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-2 rounded-2xl
                       border border-white/30 bg-white/10 px-6 py-3.5
                       font-extrabold text-white backdrop-blur-sm
                       transition hover:-translate-y-0.5 hover:bg-white/20"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="5"/>
                    <circle cx="12" cy="12" r="4"/>
                    <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
                </svg>

                Follow on Instagram
            </a>

        </div>
    </div>
</section>
@endsection
