@extends('layouts.site')

@section('content')

{{-- =========================================================
  HERO / JOB HEADER
========================================================= --}}
<section class="bg-slate-950 text-white">
  <div class="max-w-[1400px] mx-auto px-4 py-14 md:py-18">
    <a href="{{ route('careers.index') }}"
       class="inline-flex items-center gap-2 text-emerald-300 font-extrabold hover:underline">
      ← Back to careers
    </a>

    <h1 class="mt-5 text-4xl md:text-6xl font-extrabold leading-tight">
      {{ $job->title }}
    </h1>

    {{-- Meta chips --}}
    <div class="mt-6 flex flex-wrap items-center gap-3 text-sm">
      @if($job->employment_type)
        <span class="rounded-full bg-white/10 border border-white/10 px-4 py-2 font-semibold">
          {{ $job->employment_type }}
        </span>
      @endif

      @if($job->location)
        <span class="rounded-full bg-white/10 border border-white/10 px-4 py-2 font-semibold">
          {{ $job->location }}
        </span>
      @endif

      @if($job->salary)
        <span class="rounded-full bg-emerald-600/20 border border-emerald-400/30 px-4 py-2 font-semibold text-emerald-300">
          {{ $job->salary }}
        </span>
      @endif

      @if($job->closing_date)
        <span class="rounded-full bg-amber-500/20 border border-amber-400/30 px-4 py-2 font-semibold text-amber-200">
          Closes {{ $job->closing_date->format('d M Y') }}
        </span>
      @endif
    </div>
  </div>
</section>

{{-- =========================================================
  CONTENT + STICKY APPLY
========================================================= --}}
<section class="bg-gradient-to-b from-white to-slate-50">
  <div class="max-w-[1400px] mx-auto px-4 py-16">
    <div class="grid gap-10 lg:grid-cols-12">

      {{-- MAIN CONTENT --}}
      <article class="lg:col-span-8">
        <div class="rounded-3xl bg-white border border-slate-200 p-8 md:p-10
                    shadow-[0_14px_40px_rgba(15,23,42,0.10)]">

          <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">
            Role overview
          </h2>

          <div class="mt-6 prose prose-slate max-w-none
                      prose-headings:font-extrabold
                      prose-h2:text-2xl
                      prose-h3:text-xl">
            {!! $job->description !!}
          </div>

          {{-- Bottom CTA --}}
          <div class="mt-10 pt-8 border-t border-slate-200">
            <a href="{{ route('careers.apply', $job->slug) }}"
               class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-8 py-4
                      text-white font-extrabold hover:bg-emerald-700 transition">
              Apply for this role →
            </a>
          </div>
        </div>
      </article>

      {{-- STICKY SIDEBAR --}}
      <aside class="lg:col-span-4">
        <div class="sticky top-28 space-y-6">

          {{-- Apply card --}}
          <div class="rounded-3xl bg-white border border-slate-200 p-6
                      shadow-[0_14px_40px_rgba(15,23,42,0.10)]">
            <h3 class="text-xl font-extrabold text-slate-900">
              Interested in this role?
            </h3>

            <p class="mt-3 text-slate-600">
              Apply online — it only takes a few minutes.
            </p>

            <a href="{{ route('careers.apply', $job->slug) }}"
               class="mt-6 inline-flex w-full items-center justify-center rounded-2xl
                      bg-emerald-600 px-6 py-4 text-white font-extrabold
                      hover:bg-emerald-700 transition">
              Apply now
            </a>
          </div>

          {{-- Job summary --}}
          <div class="rounded-3xl bg-slate-50 border border-slate-200 p-6">
            <h4 class="text-sm font-extrabold uppercase tracking-wide text-slate-700">
              Job summary
            </h4>

            <div class="mt-4 space-y-3 text-sm text-slate-700">
              @if($job->employment_type)
                <div>
                  <span class="font-semibold text-slate-900">Type:</span>
                  {{ $job->employment_type }}
                </div>
              @endif

              @if($job->location)
                <div>
                  <span class="font-semibold text-slate-900">Location:</span>
                  {{ $job->location }}
                </div>
              @endif

              @if($job->salary)
                <div>
                  <span class="font-semibold text-slate-900">Salary:</span>
                  {{ $job->salary }}
                </div>
              @endif

              @if($job->closing_date)
                <div>
                  <span class="font-semibold text-slate-900">Closing date:</span>
                  {{ $job->closing_date->format('d M Y') }}
                </div>
              @endif
            </div>
          </div>

        </div>
      </aside>

    </div>
  </div>
</section>

@endsection
