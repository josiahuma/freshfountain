<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root{
            --brand: 0 74 173;        /* #004AAD */
            --brand-dark: 0 59 138;   /* #003B8A */
            --navy: 5 10 48;          /* #050A30 */
            --dark: 2 6 23;           /* near-black */
        }

        .ff-header {
            background: rgba(2, 6, 23, 0.72) !important;
            -webkit-backdrop-filter: blur(14px);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255,255,255,0.10) !important;
        }
        .ff-nav a { color: rgba(255,255,255,0.82) !important; }
        .ff-nav a:hover { color: rgba(255,255,255,1) !important; }

        /* -------------------------------------------------------
           MOBILE MENU (NO JS) — checkbox toggles menu + backdrop
        -------------------------------------------------------- */

        /* Backdrop hidden by default */
        #ffMenuBackdrop{
            position: fixed;
            inset: 0;
            background: rgba(2, 6, 23, 0.55);
            opacity: 0;
            pointer-events: none;
            transition: opacity .18s ease;
            z-index: 9998;
        }

        /* Menu container hidden by default */
        #mobileMenu{
            position: fixed;
            left: 0;
            right: 0;
            top: var(--ff-header-h, 72px);
            transform: translateY(-10px);
            opacity: 0;
            pointer-events: none;
            transition: transform .18s ease, opacity .18s ease;
            z-index: 9999;
        }

        /* When checkbox is checked -> show */
        #navToggle:checked ~ #ffMenuBackdrop{
            opacity: 1;
            pointer-events: auto;
        }
        #navToggle:checked ~ #mobileMenu{
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        /* Make menu scroll if tall */
        .ff-mobile-inner{
            max-height: calc(100vh - var(--ff-header-h, 72px));
            overflow: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Compute header height safely (fallback is 72px) */
        header.ff-header { height: auto; }
    </style>
</head>

<body class="bg-white text-gray-900">
@php
    $homePage = \App\Models\Page::query()->where('slug', 'home')->first();
    $headerData = $homePage ? data_get($homePage->sections, 'header', []) : [];

    $logoPath = data_get($headerData, 'logo');
    $siteName = data_get($headerData, 'site_name', config('app.name'));

    $footer = $homePage ? data_get($homePage->sections, 'footer', []) : [];
    $socialLinks = data_get($footer, 'social_links', []);
    $social = is_array($socialLinks) ? $socialLinks : [];

    $findSocial = function(string $name) use ($social) {
        foreach ($social as $s) {
            if (strtolower((string) data_get($s, 'platform')) === strtolower($name) && data_get($s, 'url')) {
                return data_get($s, 'url');
            }
        }
        return null;
    };

    $facebook  = $findSocial('facebook');
    $instagram = $findSocial('instagram');
    $youtube   = $findSocial('youtube');
    $tiktok    = $findSocial('tiktok');
    $x         = $findSocial('twitter') ?? $findSocial('x');
@endphp

{{-- ✅ The toggle checkbox (must be BEFORE backdrop + menu) --}}
<input id="navToggle" type="checkbox" class="hidden" />

{{-- ✅ Backdrop: clicking it closes menu (label toggles checkbox off) --}}
<label for="navToggle" id="ffMenuBackdrop" aria-hidden="true"></label>

{{-- HEADER --}}
<header id="ffHeader" class="ff-header sticky top-0 z-[10000]">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-center justify-between py-4">

            {{-- Brand --}}
            <a href="/" class="flex items-center gap-3">
                @if($logoPath)
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}"
                        alt="{{ $siteName }}"
                        class="h-10 md:h-11 w-auto"
                    />
                @else
                    <span class="font-extrabold text-xl tracking-tight text-white">{{ $siteName }}</span>
                @endif
            </a>

            {{-- Desktop Nav --}}
            <nav class="ff-nav hidden md:flex items-center gap-8 font-semibold">
                <a href="/" class="transition">Home</a>
                <a href="/#about-section" class="transition">About</a>
                <a href="/#watch" class="transition">Watch Services</a>
                <a href="/#event-section" class="transition">What's On</a>
                <a href="/events" class="transition">Major Events</a>
                <a href="/units" class="transition">Get Involved</a>
                <a href="/courses" class="transition">Courses</a>
                <a href="/contact" class="transition">Contact</a>

                {{-- Social icons --}}
                <div class="hidden lg:flex items-center gap-2">
                    @if($facebook)
                        <a href="{{ $facebook }}" target="_blank" rel="noopener"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 border border-white/10 hover:bg-white/15 transition"
                           aria-label="Facebook">
                            @includeIf('partials.social-icons.facebook')
                        </a>
                    @endif
                    @if($instagram)
                        <a href="{{ $instagram }}" target="_blank" rel="noopener"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 border border-white/10 hover:bg-white/15 transition"
                           aria-label="Instagram">
                            @includeIf('partials.social-icons.instagram')
                        </a>
                    @endif
                    @if($youtube)
                        <a href="{{ $youtube }}" target="_blank" rel="noopener"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 border border-white/10 hover:bg-white/15 transition"
                           aria-label="YouTube">
                            @includeIf('partials.social-icons.youtube')
                        </a>
                    @endif
                    @if($tiktok)
                        <a href="{{ $tiktok }}" target="_blank" rel="noopener"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 border border-white/10 hover:bg-white/15 transition"
                           aria-label="TikTok">
                            @includeIf('partials.social-icons.tiktok')
                        </a>
                    @endif
                    @if($x)
                        <a href="{{ $x }}" target="_blank" rel="noopener"
                           class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 border border-white/10 hover:bg-white/15 transition"
                           aria-label="X">
                            @includeIf('partials.social-icons.twitter')
                        </a>
                    @endif
                </div>

                {{-- CTA --}}
                <a href="/contact"
                   class="ml-2 inline-flex items-center justify-center rounded-xl
                          bg-[rgb(var(--brand))] px-5 py-2.5 text-white shadow-sm
                          hover:bg-[rgb(var(--brand-dark))] transition">
                    Giving
                </a>
            </nav>

            {{-- ✅ Mobile button: label toggles checkbox --}}
            <label
                for="navToggle"
                class="md:hidden inline-flex items-center justify-center rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-white/90 shadow-sm cursor-pointer select-none"
                aria-controls="mobileMenu"
            >
                ☰
            </label>
        </div>
    </div>

    {{-- Set header height CSS var via inline style (simple + reliable) --}}
    <style>
        :root { --ff-header-h: 72px; }
        @media (min-width: 768px) { :root { --ff-header-h: 72px; } }
    </style>
</header>

{{-- MOBILE MENU (fixed; not inside header stacking issues) --}}
<div id="mobileMenu" class="md:hidden fixed inset-0 z-[9999] bg-[rgb(var(--dark))]">

    <div class="ff-mobile-inner relative z-10 border-t border-white/10 bg-[rgb(var(--dark))]">
        <div class="max-w-[1400px] mx-auto px-4 py-5 flex flex-col gap-4 font-semibold text-white/85">

            {{-- ✅ Clicking any link closes menu by toggling checkbox off --}}
            <label for="navToggle" class="hidden" aria-hidden="true"></label>

            <a href="/" class="hover:text-white transition" onclick="document.getElementById('navToggle').checked=false">Home</a>
            <a href="/#about-section" class="hover:text-white transition" onclick="document.getElementById('navToggle').checked=false">About</a>
            <a href="/#watch" class="hover:text-white transition" onclick="document.getElementById('navToggle').checked=false">Watch Services</a>
            <a href="/#event-section" class="hover:text-white transition" onclick="document.getElementById('navToggle').checked=false">What's On</a>
            <a href="/events" class="hover:text-white transition" onclick="document.getElementById('navToggle').checked=false">Major Events</a>
            <a href="/units" class="hover:text-white transition" onclick="document.getElementById('navToggle').checked=false">Get Involved</a>
            <a href="/courses" class="hover:text-white transition" onclick="document.getElementById('navToggle').checked=false">Courses</a>
            <a href="/contact" class="hover:text-white transition" onclick="document.getElementById('navToggle').checked=false">Contact</a>

            <div class="flex flex-wrap items-center gap-2 pt-2">
                @if($facebook)
                    <a href="{{ $facebook }}" target="_blank" rel="noopener"
                       class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 border border-white/10 hover:bg-white/15 transition"
                       aria-label="Facebook">
                        @includeIf('partials.social-icons.facebook')
                    </a>
                @endif
                @if($instagram)
                    <a href="{{ $instagram }}" target="_blank" rel="noopener"
                       class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 border border-white/10 hover:bg-white/15 transition"
                       aria-label="Instagram">
                        @includeIf('partials.social-icons.instagram')
                    </a>
                @endif
                @if($youtube)
                    <a href="{{ $youtube }}" target="_blank" rel="noopener"
                       class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 border border-white/10 hover:bg-white/15 transition"
                       aria-label="YouTube">
                        @includeIf('partials.social-icons.youtube')
                    </a>
                @endif
            </div>

            <a href="/contact"
               class="mt-2 inline-flex items-center justify-center rounded-xl
                      bg-[rgb(var(--brand))] px-5 py-3 text-white shadow-sm
                      hover:bg-[rgb(var(--brand-dark))] transition"
               onclick="document.getElementById('navToggle').checked=false">
                Get in touch
            </a>

            {{-- Close button --}}
            <label for="navToggle"
                   class="mt-2 inline-flex items-center justify-center rounded-xl border border-white/15 bg-white/5 px-5 py-3 text-white/90 cursor-pointer">
                Close
            </label>

        </div>
    </div>
</div>

<main>
    @yield('content')
</main>

{{-- FOOTER --}}
<footer class="bg-black text-white">
    <div class="max-w-[1400px] mx-auto px-4 py-14">
        <div class="grid gap-10 lg:grid-cols-5 items-start">

            <div class="lg:col-span-2">
                <div class="flex items-center gap-4">
                    @if($logoPath)
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}"
                            alt="{{ $siteName }}"
                            class="h-10 w-auto opacity-95"
                        />
                    @endif
                    <div>
                        <div class="text-2xl font-extrabold tracking-tight">
                            {{ data_get($footer, 'company_name', 'Fresh Fountain Christian Network') }}
                        </div>
                        @if(data_get($footer, 'tagline'))
                            <div class="text-white/70 font-semibold mt-1">
                                {{ data_get($footer, 'tagline') }}
                            </div>
                        @endif
                    </div>
                </div>

                <p class="mt-6 text-white/70 leading-relaxed max-w-xl">
                    {{ data_get($footer, 'about_text', 'We are a faith-led community committed to worship, discipleship, and outreach. Join us as we grow together and serve our city with love.') }}
                </p>
            </div>

            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-white/80">Our Mandate</h3>
                <ul class="mt-5 space-y-3 text-white/80">
                    <li><a href="/about-us" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Vision</a></li>
                    <li><a href="/about-us" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Mission</a></li>
                    <li><a href="/about-us" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Core Values</a></li>
                    <li><a href="/leaders" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Leadership</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-white/80">Get Involved</h3>
                <ul class="mt-5 space-y-3 text-white/80">
                    <li><a href="/membership" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Become a member</a></li>
                    <li><a href="/baptism" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Get baptized</a></li>
                    <li><a href="/units" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Join a unit</a></li>
                    <li><a href="/contact" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Speak to a leader</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-white/80">Links</h3>
                <ul class="mt-5 space-y-3 text-white/80">
                    @php $links = data_get($footer, 'links', []); @endphp
                    @forelse($links as $link)
                        <li>
                            <a href="{{ data_get($link, 'url', '#') }}"
                               class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">
                                {{ data_get($link, 'label', 'Link') }}
                            </a>
                        </li>
                    @empty
                        <li><a href="/recruitment" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Vacancies</a></li>
                        <li><a href="/safeguarding" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Safeguarding</a></li>
                        <li><a href="/media-enquiries" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Media Enquiries</a></li>
                    @endforelse
                </ul>

                <div class="mt-8 rounded-2xl bg-white/5 border border-white/10 p-4">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-white/60">Contact</div>
                    <div class="mt-3 space-y-2 text-white/80">
                        @if(data_get($footer, 'phone_primary'))
                            <div class="font-semibold">{{ data_get($footer, 'phone_primary') }}</div>
                        @endif
                        @if(data_get($footer, 'email'))
                            <div>
                                <a class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition"
                                   href="mailto:{{ data_get($footer, 'email') }}">
                                    {{ data_get($footer, 'email') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 border-t border-white/10 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-white/60 text-sm">
            <div>
                © {{ date('Y') }} {{ data_get($footer, 'company_name', 'Fresh Fountain Christian Network') }}. All rights reserved.
            </div>
            <div class="flex items-center gap-4">
                <a href="/privacy" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Privacy</a>
                <a href="/terms" class="hover:text-white underline underline-offset-4 decoration-white/30 hover:decoration-white transition">Terms</a>
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
