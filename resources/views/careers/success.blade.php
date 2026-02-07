@extends('layouts.site')

@section('content')
<section class="bg-slate-50">
  <div class="max-w-[1000px] mx-auto px-4 py-24">

    <div class="relative overflow-hidden rounded-3xl bg-white border border-slate-200
                shadow-[0_22px_70px_rgba(15,23,42,0.12)] p-10 md:p-14 text-center">

      {{-- Decorative accent --}}
      <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-emerald-500/10"></div>
      <div class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-emerald-500/10"></div>

      {{-- Icon --}}
      <div class="relative z-10 mx-auto flex h-20 w-20 items-center justify-center rounded-3xl
                  bg-emerald-600 text-white shadow-lg">
        ✓
      </div>

      <h1 class="relative z-10 mt-8 text-3xl md:text-5xl font-extrabold text-slate-900">
        Application received
      </h1>

      <p class="relative z-10 mt-5 text-slate-600 text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
        Thank you for applying for the role of
        <span class="font-extrabold text-slate-900">{{ $job->title }}</span>.
        Your application has been successfully submitted and is now under review.
      </p>

      {{-- What happens next --}}
      <div class="relative z-10 mt-10 grid gap-5 md:grid-cols-3 text-left">
        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">
          <div class="text-sm font-extrabold text-emerald-700 uppercase tracking-wide">
            Step 1
          </div>
          <p class="mt-2 font-semibold text-slate-900">
            Application review
          </p>
          <p class="mt-1 text-sm text-slate-600">
            Our recruitment team will carefully review your application.
          </p>
        </div>

        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">
          <div class="text-sm font-extrabold text-emerald-700 uppercase tracking-wide">
            Step 2
          </div>
          <p class="mt-2 font-semibold text-slate-900">
            Contact & screening
          </p>
          <p class="mt-1 text-sm text-slate-600">
            If shortlisted, we’ll contact you to discuss next steps.
          </p>
        </div>

        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">
          <div class="text-sm font-extrabold text-emerald-700 uppercase tracking-wide">
            Step 3
          </div>
          <p class="mt-2 font-semibold text-slate-900">
            Interview & onboarding
          </p>
          <p class="mt-1 text-sm text-slate-600">
            Successful candidates progress to interview and onboarding.
          </p>
        </div>
      </div>

      {{-- Actions --}}
      <div class="relative z-10 mt-14 flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="{{ route('careers.index') }}"
           class="inline-flex items-center justify-center rounded-2xl
                  bg-emerald-600 px-8 py-3 font-extrabold text-white
                  hover:bg-emerald-700 transition">
          View more roles →
        </a>

        <a href="/jobs"
           class="inline-flex items-center justify-center rounded-2xl
                  border border-slate-200 bg-white px-8 py-3
                  font-extrabold text-slate-900 hover:bg-slate-100 transition">
          Back to recruitment
        </a>
      </div>

      {{-- Support line --}}
      <p class="relative z-10 mt-10 text-sm text-slate-500">
        If you have any questions, feel free to contact our recruitment team.
      </p>

    </div>

  </div>
</section>
@endsection
