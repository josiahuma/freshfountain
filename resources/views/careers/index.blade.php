@extends('layouts.site')

@section('content')

{{-- =========================================================
  HERO — Careers hub
========================================================= --}}
<section class="bg-slate-950 text-white">
  <div class="max-w-[1400px] mx-auto px-4 py-16 md:py-20">
    <div class="max-w-3xl">
      <span class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/10 px-4 py-2 text-sm font-extrabold text-emerald-300">
        Join our team
      </span>

      <h1 class="mt-5 text-4xl md:text-6xl font-extrabold leading-tight">
        Careers at PT Care
      </h1>

      <p class="mt-5 text-white/80 text-lg leading-relaxed max-w-2xl">
        Browse our current vacancies and apply online to become part of a
        compassionate, professional care team.
      </p>
    </div>
  </div>
</section>

{{-- =========================================================
  JOB LISTING
========================================================= --}}
<section class="bg-gradient-to-b from-white to-slate-50">
  <div class="max-w-[1400px] mx-auto px-4 py-16">

    {{-- Meta row --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
      <div>
        <p class="text-sm font-extrabold uppercase tracking-wide text-emerald-700">
          Current vacancies
        </p>
        <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-slate-900">
          Open roles
        </h2>
      </div>

      {{-- Static filter chips (future-proof) --}}
      <div class="flex flex-wrap gap-2">
        <span class="rounded-full bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">
          Care roles
        </span>
        <span class="rounded-full bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">
          Support staff
        </span>
        <span class="rounded-full bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">
          Flexible hours
        </span>
      </div>
    </div>

    {{-- Jobs --}}
    @if($jobs->count())
      <div class="mt-12 grid gap-6 lg:grid-cols-2">

        @foreach($jobs as $job)
          <article
            class="group rounded-3xl bg-white border border-slate-200 p-7
                   shadow-[0_14px_40px_rgba(15,23,42,0.10)]
                   hover:shadow-[0_22px_70px_rgba(15,23,42,0.18)]
                   transition-all duration-300 hover:-translate-y-1">

            {{-- Top --}}
            <div class="flex items-start justify-between gap-4">
              <div>
                <h3 class="text-xl md:text-2xl font-extrabold text-slate-900 leading-snug">
                  {{ $job->title }}
                </h3>

                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                  @if($job->employment_type)
                    <span class="rounded-full bg-emerald-50 px-3 py-1 font-semibold text-emerald-700">
                      {{ $job->employment_type }}
                    </span>
                  @endif

                  @if($job->location)
                    <span class="rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-700">
                      {{ $job->location }}
                    </span>
                  @endif

                  @if($job->closing_date)
                    <span class="rounded-full bg-amber-50 px-3 py-1 font-semibold text-amber-800">
                      Closes {{ $job->closing_date->format('d M Y') }}
                    </span>
                  @endif
                </div>
              </div>

              <div class="text-emerald-600 text-2xl transition group-hover:translate-x-1">
                →
              </div>
            </div>

            {{-- Salary --}}
            @if($job->salary)
              <p class="mt-4 text-slate-700">
                <span class="font-extrabold text-slate-900">Salary:</span>
                {{ $job->salary }}
              </p>
            @endif

            {{-- CTA --}}
            <div class="mt-6 flex flex-wrap items-center gap-3">
              <a href="{{ route('careers.show', $job->slug) }}"
                 class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 text-white font-extrabold hover:bg-emerald-700 transition">
                View & apply
              </a>

              <a href="{{ route('careers.show', $job->slug) }}"
                 class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-6 py-3 font-extrabold text-slate-800 hover:bg-slate-100 transition">
                Role details
              </a>
            </div>

          </article>
        @endforeach
      </div>

      {{-- Pagination --}}
      <div class="mt-14">
        {{ $jobs->links() }}
      </div>

    @else
      {{-- Empty state --}}
      <div class="mt-12 rounded-3xl bg-white border border-slate-200 p-12 text-center shadow-[0_10px_30px_rgba(15,23,42,0.08)]">
        <h3 class="text-2xl font-extrabold text-slate-900">
          No vacancies right now
        </h3>
        <p class="mt-3 text-slate-600 max-w-xl mx-auto">
          We don’t have any open roles at the moment, but we’re always happy to hear from passionate carers.
          Please check back soon.
        </p>

        <div class="mt-6">
          <a href="/contact-us"
             class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 text-white font-extrabold hover:bg-emerald-700 transition">
            Contact us
          </a>
        </div>
      </div>
    @endif

  </div>
</section>

@endsection
