@extends('layouts.site')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;

  $banner = $page->banner_image ? Storage::url($page->banner_image) : null;

  // Pull a few other service pages (NOT from home.sections)
  $moreServices = \App\Models\Page::query()
      ->where('is_published', true)
      ->where('template', 'service')
      ->where('id', '!=', $page->id)
      ->orderBy('title')
      ->limit(6)
      ->get();

  // Handy links (edit to match your routes)
  $watchUrl   = '/#watch';
  $visitUrl   = '/#visit';
  $prayerUrl  = '/contact'; // or /prayer-request if you have one
@endphp

<style>
  .ff-soft { box-shadow: 0 18px 55px rgba(15,23,42,.10); }
  .ff-glow { box-shadow: 0 30px 90px rgba(0,0,0,.35); }
</style>

{{-- =========================================================
  HERO — Fresh Fountain “editorial church” style
========================================================= --}}
<section class="relative overflow-hidden bg-slate-950 text-white">
  @if($banner)
    <img
      src="{{ $banner }}"
      class="absolute inset-0 w-full h-full object-cover opacity-45"
      alt="{{ $page->title }}"
    >
  @endif

  {{-- overlays --}}
  <div class="absolute inset-0 bg-black/55"></div>
  <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/25"></div>
  <div class="absolute inset-0 opacity-80 bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.12),transparent_55%)]"></div>

  <div class="relative max-w-[1400px] mx-auto px-4 py-24 md:py-32">
    <div class="max-w-3xl">
      <p class="text-sm font-extrabold uppercase tracking-widest text-white/70">
        Ministry & Services
      </p>

      <h1 class="mt-4 text-4xl md:text-6xl font-extrabold leading-tight">
        {{ $page->title }}
      </h1>

      @if($page->excerpt)
        <p class="mt-6 text-lg md:text-xl text-white/85 leading-relaxed max-w-2xl">
          {{ $page->excerpt }}
        </p>
      @endif

      <div class="mt-10 flex flex-wrap gap-3">
        <a href="{{ $visitUrl }}"
           class="inline-flex items-center justify-center rounded-2xl bg-[rgb(var(--brand))] px-7 py-3 font-extrabold text-white hover:bg-[rgb(var(--brand-dark))] transition">
          Plan a visit →
        </a>

        <a href="{{ $watchUrl }}"
           class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-7 py-3 font-extrabold text-white hover:bg-white/10 transition">
          Watch online
        </a>

        <a href="{{ $prayerUrl }}"
           class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-black/25 px-7 py-3 font-extrabold text-white hover:bg-black/35 transition">
          Request prayer
        </a>
      </div>
    </div>
  </div>
</section>

{{-- =========================================================
  CONTENT — clean, church-friendly typography
========================================================= --}}
<section class="bg-white">
  <div class="max-w-[1100px] mx-auto px-4 py-16">
    <article class="prose prose-slate prose-lg max-w-none prose-headings:font-extrabold prose-a:text-[rgb(var(--brand))]">
      {!! $page->content !!}
    </article>
  </div>
</section>

{{-- =========================================================
  NEXT STEPS — simple church cards
========================================================= --}}
<section class="bg-slate-50">
  <div class="max-w-[1400px] mx-auto px-4 py-16">
    <div class="grid gap-6 md:grid-cols-3">
      <div class="rounded-[32px] border border-slate-200 bg-white p-8 ff-soft">
        <div class="text-sm font-extrabold uppercase tracking-wide text-[rgb(var(--brand))]">New here?</div>
        <div class="mt-2 text-2xl font-extrabold text-slate-900">Plan your visit</div>
        <div class="mt-3 text-slate-600 leading-relaxed">
          Find service times, directions, and what to expect when you arrive.
        </div>
        <a href="{{ $visitUrl }}" class="mt-6 inline-flex font-extrabold text-[rgb(var(--brand))] hover:text-[rgb(var(--brand-dark))] transition">
          Plan a visit →
        </a>
      </div>

      <div class="rounded-[32px] border border-slate-200 bg-white p-8 ff-soft">
        <div class="text-sm font-extrabold uppercase tracking-wide text-[rgb(var(--brand))]">Watch</div>
        <div class="mt-2 text-2xl font-extrabold text-slate-900">Catch the latest</div>
        <div class="mt-3 text-slate-600 leading-relaxed">
          Join the livestream or replay the most recent service.
        </div>
        <a href="{{ $watchUrl }}" class="mt-6 inline-flex font-extrabold text-[rgb(var(--brand))] hover:text-[rgb(var(--brand-dark))] transition">
          Watch online →
        </a>
      </div>

      <div class="rounded-[32px] border border-slate-200 bg-white p-8 ff-soft">
        <div class="text-sm font-extrabold uppercase tracking-wide text-[rgb(var(--brand))]">Need prayer?</div>
        <div class="mt-2 text-2xl font-extrabold text-slate-900">We’re with you</div>
        <div class="mt-3 text-slate-600 leading-relaxed">
          Send a request and our team will stand with you in faith.
        </div>
        <a href="{{ $prayerUrl }}" class="mt-6 inline-flex font-extrabold text-[rgb(var(--brand))] hover:text-[rgb(var(--brand-dark))] transition">
          Request prayer →
        </a>
      </div>
    </div>
  </div>
</section>

{{-- =========================================================
  MORE SERVICES — pulls other Service pages (NOT home repeater)
========================================================= --}}
@if($moreServices->count())
<section class="bg-white">
  <div class="max-w-[1400px] mx-auto px-4 py-16">

    <div class="flex items-end justify-between gap-6 flex-wrap">
      <div>
        <p class="text-sm font-extrabold uppercase tracking-wide text-slate-500">More</p>
        <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-slate-900">
          Explore other services
        </h2>
        <p class="mt-2 text-slate-600 max-w-2xl">
          Find a place to grow, serve, and belong.
        </p>
      </div>

      <a href="/services"
         class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 font-extrabold text-slate-900 hover:bg-slate-50 transition">
        View all →
      </a>
    </div>

    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      @foreach($moreServices as $svc)
        @php
          $svcBanner = $svc->banner_image ? Storage::url($svc->banner_image) : null;
        @endphp

        <a href="/{{ $svc->slug }}"
           class="group block rounded-[34px] overflow-hidden border border-slate-200 bg-white
                  shadow-[0_14px_40px_rgba(15,23,42,0.10)]
                  hover:shadow-[0_22px_70px_rgba(15,23,42,0.18)] transition-all duration-300 hover:-translate-y-1">

          <div class="relative aspect-[16/10] bg-slate-100 overflow-hidden">
            @if($svcBanner)
              <img src="{{ $svcBanner }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700" alt="">
            @else
              <div class="absolute inset-0 bg-gradient-to-tr from-[rgb(var(--brand))]/25 via-slate-900/20 to-transparent"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-transparent to-transparent"></div>
            <div class="absolute bottom-4 left-4 right-4">
              <div class="text-white font-extrabold text-lg leading-snug">
                {{ $svc->title }}
              </div>
              @if($svc->excerpt)
                <div class="mt-1 text-white/80 text-sm">
                  {{ Str::limit($svc->excerpt, 90) }}
                </div>
              @endif
            </div>
          </div>

          <div class="p-6">
            <div class="inline-flex items-center gap-2 font-extrabold text-[rgb(var(--brand))]">
              View details <span class="transition group-hover:translate-x-1">→</span>
            </div>
          </div>
        </a>
      @endforeach
    </div>

  </div>
</section>
@endif

@endsection
