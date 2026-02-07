@extends('layouts.site')

@section('content')
@php
  /** @var \App\Models\BlogPost $post */

  $banner = $post->featured_image ? asset('storage/' . $post->featured_image) : null;
  $date = $post->published_at ? $post->published_at->format('d M Y') : null;

  // Optional: if you later add categories/tags, you can surface them here.
@endphp

{{-- HERO / BANNER (PT Care style) --}}
<header class="relative">
  <div class="relative h-[320px] md:h-[520px] overflow-hidden bg-slate-900">
    @if($banner)
      <img src="{{ $banner }}" class="absolute inset-0 h-full w-full object-cover" alt="{{ $post->title }}">
    @endif

    {{-- PT Care overlay --}}
    <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/85 via-slate-900/55 to-emerald-900/25"></div>

    {{-- Decorative soft glow --}}
    <div class="absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-emerald-500/15 blur-3xl"></div>
    <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative h-full">
      <div class="max-w-[1100px] mx-auto px-4 h-full flex items-end">
        <div class="pb-10 md:pb-14 w-full">

          <a href="/blog"
             class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-white/90 text-sm font-extrabold hover:bg-white/15 transition">
            <span class="text-emerald-300">←</span> Back to Blog
          </a>

          @if($date)
            <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-white/90">
              <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
              {{ $date }}
            </div>
          @endif

          <h1 class="mt-4 text-3xl md:text-6xl font-extrabold text-white leading-tight">
            {{ $post->title }}
          </h1>

          @if($post->excerpt)
            <p class="mt-5 max-w-3xl text-white/85 text-base md:text-xl leading-relaxed">
              {{ $post->excerpt }}
            </p>
          @endif

        </div>
      </div>
    </div>
  </div>

  {{-- Floating info strip (PT Care vibe) --}}
  <div class="bg-white">
    <div class="max-w-[1100px] mx-auto px-4 -mt-8 relative z-10">
      <div class="rounded-3xl border border-slate-200 bg-white shadow-[0_18px_60px_rgba(15,23,42,0.12)] p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-emerald-600/10 flex items-center justify-center text-emerald-700 font-extrabold">
              ✓
            </div>
            <div>
              <div class="font-extrabold text-slate-900">PT Care News</div>
              <div class="text-sm text-slate-600">Updates, guidance, and community stories.</div>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-slate-50 border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Reliable carers</span>
            <span class="rounded-full bg-slate-50 border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Fast response</span>
            <span class="rounded-full bg-slate-50 border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Quality care</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

{{-- BODY --}}
<section class="bg-slate-50">
  <div class="max-w-[1100px] mx-auto px-4 py-14">

    <div class="grid gap-8 lg:grid-cols-12">

      {{-- CONTENT --}}
      <div class="lg:col-span-8">
        <article class="rounded-3xl bg-white border border-slate-200 shadow-[0_14px_40px_rgba(15,23,42,0.10)] p-6 md:p-10">
          <div class="prose prose-slate max-w-none prose-headings:font-extrabold prose-a:text-emerald-700 prose-a:font-bold">
            {!! $post->content !!}
          </div>
        </article>

        {{-- Bottom navigation --}}
        <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
          <a href="/blog"
             class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 font-extrabold text-slate-900 hover:bg-slate-100 transition">
            ← Back to Blog
          </a>

          <a href="/contact"
             class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 font-extrabold text-white hover:bg-emerald-700 transition">
            Talk to us →
          </a>
        </div>
      </div>

      {{-- SIDEBAR --}}
      <aside class="lg:col-span-4">
        <div class="space-y-6 lg:sticky lg:top-24">

          {{-- CTA card --}}
          <div class="rounded-3xl bg-slate-950 text-white border border-white/10 overflow-hidden shadow-[0_14px_40px_rgba(15,23,42,0.10)]">
            <div class="p-7">
              <p class="text-emerald-300 font-extrabold text-xs uppercase tracking-wide">Need care support?</p>
              <h3 class="mt-2 text-2xl font-extrabold leading-tight">We’re here to help.</h3>
              <p class="mt-2 text-white/80 leading-relaxed">
                Speak to our team about home care, live-in care, or staffing support.
              </p>

              <div class="mt-6 grid gap-3">
                <a href="/contact"
                   class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 font-extrabold hover:bg-emerald-700 transition">
                  Contact us →
                </a>
                <a href="/services"
                   class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-6 py-3 font-extrabold hover:bg-white/10 transition">
                  View services
                </a>
              </div>
            </div>
          </div>

          {{-- Quick links --}}
          <div class="rounded-3xl bg-white border border-slate-200 shadow-[0_14px_40px_rgba(15,23,42,0.08)] p-7">
            <h4 class="text-lg font-extrabold text-slate-900">Explore</h4>
            <div class="mt-4 grid gap-2 text-slate-700 font-semibold">
              <a href="/services" class="inline-flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 hover:bg-slate-100 transition">
                Our Services <span class="text-emerald-700">→</span>
              </a>
              <a href="/contact" class="inline-flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 hover:bg-slate-100 transition">
                Contact <span class="text-emerald-700">→</span>
              </a>
              <a href="/jobs" class="inline-flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 hover:bg-slate-100 transition">
                Recruitment <span class="text-emerald-700">→</span>
              </a>
            </div>
          </div>

        </div>
      </aside>

    </div>
  </div>
</section>
@endsection
