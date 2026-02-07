{{-- resources/views/careers/forms/adult.blade.php --}}

<div x-show="step===1" x-cloak class="rounded-2xl bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.08)]">
  <h3 class="text-xl font-extrabold text-slate-900">Adult Carer Application</h3>
  <p class="mt-2 text-slate-600">
    This form will use the Adult template fields. For now it’s using the same 3-step wizard structure.
  </p>
  <p class="mt-4 text-sm text-slate-500">
    Next: we’ll build Adult fields step-by-step the same way as Child.
  </p>
</div>

<div x-show="step===2" x-cloak class="rounded-2xl bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.08)]">
  <p class="text-slate-600">Adult template – Step 2 placeholder.</p>
</div>

<div x-show="step===3" x-cloak class="rounded-2xl bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.08)]">
  <p class="text-slate-600">Adult template – Step 3 placeholder.</p>
</div>
