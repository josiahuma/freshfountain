@extends('layouts.site')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;

  $banner = $page?->banner_image ? Storage::url($page->banner_image) : null;

  $title = $page->title ?? 'Jobs';
  $excerpt = $page->excerpt ?? null;
@endphp

{{-- =========================================================
  HERO — Recruitment hub (PT Care style)
========================================================= --}}
<section class="relative overflow-hidden bg-slate-950 text-white">
  @if($banner)
    <img src="{{ $banner }}" class="absolute inset-0 w-full h-full object-cover opacity-35" alt="">
  @endif
  <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/70 to-emerald-900/25"></div>

  <div class="relative max-w-[1400px] mx-auto px-4 py-18 md:py-24">
    <div class="max-w-3xl">
      <span class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/10 px-4 py-2 text-sm font-extrabold text-emerald-300">
        Recruitment
      </span>

      <h1 class="mt-6 text-4xl md:text-6xl font-extrabold leading-tight">
        {{ $title }}
      </h1>

      @if($excerpt)
        <p class="mt-6 text-lg md:text-xl text-white/80 leading-relaxed max-w-2xl">
          {{ $excerpt }}
        </p>
      @endif

      <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('careers.index') }}"
           class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 font-extrabold text-white hover:bg-emerald-700 transition">
          View vacancies →
        </a>

        <a href="#apply-steps"
           class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-6 py-3 font-extrabold text-white hover:bg-white/10 transition">
          How to apply
        </a>
      </div>
    </div>
  </div>
</section>

{{-- =========================================================
  CONTENT + SIDE PANELS
========================================================= --}}
<section class="bg-white">
  <div class="max-w-[1400px] mx-auto px-4 py-16">
    <div class="grid gap-10 lg:grid-cols-12 lg:items-start">

      {{-- MAIN: CMS content (Recruitment guide) --}}
      <div class="lg:col-span-8">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-[0_18px_60px_rgba(15,23,42,0.10)] p-8 md:p-10">
          <p class="text-sm font-extrabold uppercase tracking-wide text-emerald-700">Recruitment guide</p>
          <h2 class="mt-2 text-2xl md:text-4xl font-extrabold text-slate-900">
            What to expect
          </h2>

          @if(!empty($page->content))
            <div class="mt-6 prose prose-slate max-w-none prose-headings:font-extrabold prose-h2:text-2xl prose-h3:text-xl">
              {!! $page->content !!}
            </div>
          @else
            <div class="mt-6 rounded-2xl bg-slate-50 border border-slate-200 p-6 text-slate-700">
              <p class="font-semibold">Recruitment information is being updated.</p>
              <p class="mt-2 text-slate-600">
                Please check back soon for recruitment information and required documents.
              </p>
            </div>
          @endif
        </div>

        {{-- Apply steps --}}
        <div id="apply-steps" class="mt-10 rounded-3xl border border-slate-200 bg-slate-50 p-8 md:p-10">
          <p class="text-sm font-extrabold uppercase tracking-wide text-emerald-700">Simple process</p>
          <h3 class="mt-2 text-2xl md:text-3xl font-extrabold text-slate-900">How to apply</h3>

          <div class="mt-8 grid gap-5 md:grid-cols-3">
            <div class="rounded-2xl bg-white border border-slate-200 p-6">
              <div class="h-10 w-10 rounded-2xl bg-emerald-600/10 text-emerald-700 flex items-center justify-center font-extrabold">1</div>
              <h4 class="mt-4 font-extrabold text-slate-900">Choose a vacancy</h4>
              <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                Browse our current roles and pick the one that suits your experience.
              </p>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-6">
              <div class="h-10 w-10 rounded-2xl bg-emerald-600/10 text-emerald-700 flex items-center justify-center font-extrabold">2</div>
              <h4 class="mt-4 font-extrabold text-slate-900">Apply online</h4>
              <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                Complete the form and upload your supporting documents (if required).
              </p>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-6">
              <div class="h-10 w-10 rounded-2xl bg-emerald-600/10 text-emerald-700 flex items-center justify-center font-extrabold">3</div>
              <h4 class="mt-4 font-extrabold text-slate-900">Interview & checks</h4>
              <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                We’ll contact you, arrange an interview, and guide you through compliance checks.
              </p>
            </div>
          </div>
        </div>
      </div>

      {{-- RIGHT: Sticky side panels --}}
      <aside class="lg:col-span-4">
        <div class="lg:sticky lg:top-24 space-y-6">

          {{-- Checklist --}}
          <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-[0_14px_40px_rgba(15,23,42,0.08)]">
            <p class="text-sm font-extrabold uppercase tracking-wide text-emerald-700">Be ready</p>
            <h3 class="mt-2 text-xl font-extrabold text-slate-900">What you’ll need</h3>

            <ul class="mt-5 space-y-3 text-slate-700">
              <li class="flex items-start gap-3">
                <span class="mt-1 h-8 w-8 rounded-2xl bg-emerald-600/10 text-emerald-700 flex items-center justify-center font-extrabold">✓</span>
                <span class="font-semibold">Right to work documents</span>
              </li>
              <li class="flex items-start gap-3">
                <span class="mt-1 h-8 w-8 rounded-2xl bg-emerald-600/10 text-emerald-700 flex items-center justify-center font-extrabold">✓</span>
                <span class="font-semibold">Updated CV (recommended)</span>
              </li>
              <li class="flex items-start gap-3">
                <span class="mt-1 h-8 w-8 rounded-2xl bg-emerald-600/10 text-emerald-700 flex items-center justify-center font-extrabold">✓</span>
                <span class="font-semibold">References (where applicable)</span>
              </li>
              <li class="flex items-start gap-3">
                <span class="mt-1 h-8 w-8 rounded-2xl bg-emerald-600/10 text-emerald-700 flex items-center justify-center font-extrabold">✓</span>
                <span class="font-semibold">DBS details (if you have one)</span>
              </li>
            </ul>

            <p class="mt-5 text-sm text-slate-600 leading-relaxed">
              Don’t worry if you’re missing something — we’ll advise you during the process.
            </p>
          </div>

          {{-- CTA --}}
          <div class="rounded-3xl bg-slate-950 text-white p-7 overflow-hidden relative">
            <div class="absolute inset-0 bg-gradient-to-tr from-slate-950 via-slate-900/60 to-emerald-900/25"></div>
            <div class="relative">
              <p class="text-sm font-extrabold uppercase tracking-wide text-emerald-300">Ready to apply?</p>
              <h3 class="mt-2 text-2xl font-extrabold">View current vacancies</h3>
              <p class="mt-3 text-white/75 leading-relaxed">
                Apply online via the Careers page and we’ll get back to you as soon as possible.
              </p>

              <div class="mt-6 grid gap-3">
                <a href="{{ route('careers.index') }}"
                   class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 font-extrabold hover:bg-emerald-700 transition">
                  View vacancies →
                </a>
                <a href="{{ route('careers.index') }}"
                   class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-6 py-3 font-extrabold hover:bg-white/10 transition">
                  Careers page
                </a>
              </div>
            </div>
          </div>

        </div>
      </aside>

    </div>
  </div>
</section>
@endsection
