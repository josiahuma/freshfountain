{{-- resources/views/careers/apply.blade.php --}}
@extends('layouts.site')

@section('content')
@php
  // PT Care input styles (emerald, premium)
  $input = "mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 placeholder-slate-400
            shadow-sm focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/25 outline-none";

  $select = $input;
  $textarea = $input;

  // Start step (controller can pass $startStep; fallback to 1)
  $startStep = $startStep ?? 1;

  // Template
  $tpl = strtolower((string) ($job->template ?? ''));
@endphp

{{-- =========================================================
  HERO (job context)
========================================================= --}}
<section class="bg-slate-950 text-white">
  <div class="max-w-[1400px] mx-auto px-4 py-12 md:py-14">
    <a href="{{ route('careers.show', $job->slug) }}"
       class="inline-flex items-center gap-2 text-emerald-300 font-extrabold hover:underline">
      ← Back to job
    </a>

    <h1 class="mt-5 text-4xl md:text-6xl font-extrabold leading-tight">
      Apply: {{ $job->title }}
    </h1>

    <p class="mt-4 text-white/80 max-w-3xl">
      Complete the application form below. After submission, our team will review your application in the dashboard.
    </p>
  </div>
</section>

<section class="bg-gradient-to-b from-white to-slate-50">
  <div class="max-w-[1400px] mx-auto px-4 py-14">

    @if ($errors->any())
      <div class="mb-10 rounded-3xl bg-red-50 border border-red-100 p-6 text-red-900">
        <div class="flex items-start gap-3">
          <div class="h-10 w-10 rounded-2xl bg-red-100 flex items-center justify-center font-extrabold">!</div>
          <div>
            <p class="font-extrabold">Please fix the errors below.</p>
            <p class="mt-1 text-sm text-red-700">We’ve taken you to the step that needs attention.</p>
          </div>
        </div>
      </div>
    @endif

    <div class="grid gap-10 lg:grid-cols-12" x-data="careerWizard({{ (int) $startStep }})">

      {{-- LEFT: Wizard + Form --}}
      <div class="lg:col-span-8">

        {{-- Wizard header --}}
        <div class="rounded-3xl bg-white border border-slate-200 p-6 md:p-8
                    shadow-[0_14px_40px_rgba(15,23,42,0.10)]">

          <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div>
              <p class="text-sm font-extrabold text-slate-600">
                Step <span x-text="step"></span> of <span x-text="total"></span>
              </p>

              <h2 class="mt-2 text-2xl md:text-3xl font-extrabold text-slate-900">
                <span x-show="step===1">Personal details</span>
                <span x-show="step===2">General information</span>
                <span x-show="step===3">Declaration</span>
              </h2>

              <p class="mt-2 text-slate-600">
                Please complete each step carefully. You can go back at any time.
              </p>
            </div>

            <div class="w-full md:w-[360px]">
              <div class="h-2.5 w-full rounded-full bg-slate-100 overflow-hidden">
                <div class="h-2.5 bg-emerald-600" :style="`width:${pct()}%`"></div>
              </div>
              <p class="mt-2 text-xs text-slate-500">
                <span x-text="pct()"></span>% complete
              </p>
            </div>
          </div>

          {{-- Step pills --}}
          <div class="mt-6 grid grid-cols-3 gap-3">
            <button type="button" @click="go(1)"
              class="rounded-2xl border px-3 py-3 text-sm font-extrabold transition"
              :class="step===1 ? 'border-emerald-600 text-emerald-700 bg-emerald-50'
                               : 'border-slate-200 text-slate-700 bg-white hover:bg-slate-50'">
              1. Personal
            </button>

            <button type="button" @click="go(2)"
              class="rounded-2xl border px-3 py-3 text-sm font-extrabold transition"
              :class="step===2 ? 'border-emerald-600 text-emerald-700 bg-emerald-50'
                               : 'border-slate-200 text-slate-700 bg-white hover:bg-slate-50'">
              2. General
            </button>

            <button type="button" @click="go(3)"
              class="rounded-2xl border px-3 py-3 text-sm font-extrabold transition"
              :class="step===3 ? 'border-emerald-600 text-emerald-700 bg-emerald-50'
                               : 'border-slate-200 text-slate-700 bg-white hover:bg-slate-50'">
              3. Declaration
            </button>
          </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('careers.submit', $job->slug) }}" class="mt-8 space-y-10" @keydown.enter.prevent="if(step === total) $event.target.form.requestSubmit()">
          @csrf

          {{-- Keep wizard step for validation redirects --}}
          <input type="hidden" name="_wizard_step" :value="step">

          {{-- Template fields --}}
          @include('careers.forms.basic', ['job' => $job, 'input' => $input, 'select' => $select, 'textarea' => $textarea])


          {{-- Navigation buttons --}}
          <div class="flex items-center justify-between gap-3">
            <button type="button" @click="back()"
              class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 font-extrabold text-slate-900 hover:bg-slate-50 transition"
              :class="step===1 ? 'opacity-50 pointer-events-none' : ''"
              :disabled="step===1">
              ← Back
            </button>

            <div class="flex items-center gap-3">
              {{-- Next --}}
              <button type="button" @click="next()"
                class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 font-extrabold text-white hover:bg-emerald-700 transition"
                :class="(step===total)
                  ? 'opacity-50 cursor-not-allowed pointer-events-none'
                  : 'hover:bg-emerald-700'"
                :disabled="step===total"
                x-show="step < total">
                Next →
              </button>

              {{-- Submit (only last step) --}}
              <button type="submit"
                class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 font-extrabold text-white hover:bg-emerald-700 transition"
                x-show="step === total">
                Submit application
              </button>
            </div>
          </div>
        </form>
      </div>

      {{-- RIGHT: Sticky help / job summary --}}
      <aside class="lg:col-span-4">
        <div class="sticky top-28 space-y-6">

          {{-- Job summary --}}
          <div class="rounded-3xl bg-white border border-slate-200 p-6
                      shadow-[0_14px_40px_rgba(15,23,42,0.10)]">
            <h3 class="text-xl font-extrabold text-slate-900">Application summary</h3>

            <div class="mt-4 space-y-3 text-sm text-slate-700">
              <div>
                <span class="font-extrabold text-slate-900">Role:</span>
                {{ $job->title }}
              </div>

              @if($job->location)
                <div>
                  <span class="font-extrabold text-slate-900">Location:</span>
                  {{ $job->location }}
                </div>
              @endif

              @if($job->employment_type)
                <div>
                  <span class="font-extrabold text-slate-900">Type:</span>
                  {{ $job->employment_type }}
                </div>
              @endif

              @if($job->salary)
                <div>
                  <span class="font-extrabold text-slate-900">Salary:</span>
                  {{ $job->salary }}
                </div>
              @endif

              @if($job->closing_date)
                <div>
                  <span class="font-extrabold text-slate-900">Closing date:</span>
                  {{ $job->closing_date->format('d M Y') }}
                </div>
              @endif
            </div>
          </div>

          {{-- Help card --}}
          <div class="rounded-3xl bg-slate-50 border border-slate-200 p-6">
            <h4 class="text-sm font-extrabold uppercase tracking-wide text-slate-700">
              Before you submit
            </h4>

            <ul class="mt-4 space-y-3 text-sm text-slate-700">
              <li class="flex gap-3">
                <span class="mt-0.5 h-7 w-7 rounded-2xl bg-emerald-600/10 text-emerald-700 flex items-center justify-center font-extrabold">✓</span>
                <span>Use your legal name and correct contact details.</span>
              </li>
              <li class="flex gap-3">
                <span class="mt-0.5 h-7 w-7 rounded-2xl bg-emerald-600/10 text-emerald-700 flex items-center justify-center font-extrabold">✓</span>
                <span>Double-check your references and dates.</span>
              </li>
              <li class="flex gap-3">
                <span class="mt-0.5 h-7 w-7 rounded-2xl bg-emerald-600/10 text-emerald-700 flex items-center justify-center font-extrabold">✓</span>
                <span>Be honest — safeguarding checks apply.</span>
              </li>
            </ul>

            <div class="mt-6 rounded-2xl bg-white border border-slate-200 p-4">
              <p class="text-sm font-semibold text-slate-900">Need help?</p>
              <p class="mt-1 text-sm text-slate-600">
                If you have questions about this application, contact our team.
              </p>
              <a href="/contact"
                 class="mt-4 inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 font-extrabold text-slate-900 hover:bg-slate-50 transition">
                Contact PT Care →
              </a>
            </div>
          </div>

        </div>
      </aside>

    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('careerWizard', (startStep = 1) => ({
      step: startStep,
      total: 3,

      // repeaters (used in Step 2)
      education: [{ qualification:'', institution:'', year:'' }],
      employment: [{ employer:'', role:'', from:'', to:'', reason:'' }],

      training: @js(old('training', [[
        'name' => '',
        'provider' => '',
        'date' => '',
        'expires' => '',
        'certificate' => '',
      ]])),

      voluntary: @js(old('voluntary', [[
        'organisation' => '',
        'role' => '',
        'from' => '',
        'to' => '',
        'reason' => '',
      ]])),

      references: @js(old('references', [[
        'name' => '',
        'relationship' => '',
        'phone' => '',
        'email' => '',
      ]])),

      go(n) { if (n >= 1 && n <= this.total) this.step = n },
      next() {
        if (this.step >= this.total) return;
        this.step++;
      },
      back() { if (this.step > 1) this.step-- },
      pct() { return Math.round((this.step / this.total) * 100) },
    }))
  })
</script>
@endpush
