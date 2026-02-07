{{-- resources/views/careers/forms/basic.blade.php --}}
{{-- Uses: $input, $select, $textarea --}}
{{-- Wizard step controlled by Alpine variable: step --}}

{{-- =========================
  STEP 1 — PERSONAL DETAILS
========================= --}}
<section x-show="step===1" x-cloak class="space-y-8">
  <div class="rounded-3xl bg-white border border-slate-200 p-6 md:p-8 shadow-[0_14px_40px_rgba(15,23,42,0.08)]">
    <h3 class="text-xl md:text-2xl font-extrabold text-slate-900">Personal details</h3>
    <p class="mt-2 text-slate-600">Tell us who you are and how we can reach you.</p>

    <div class="mt-8 grid gap-5 md:grid-cols-2">
      <div>
        <label class="text-sm font-extrabold text-slate-700">Full name <span class="text-red-600">*</span></label>
        <input name="full_name" value="{{ old('full_name') }}" class="{{ $input }}" required>
        @error('full_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="text-sm font-extrabold text-slate-700">Email <span class="text-red-600">*</span></label>
        <input type="email" name="email" value="{{ old('email') }}" class="{{ $input }}" required>
        @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="text-sm font-extrabold text-slate-700">Phone <span class="text-red-600">*</span></label>
        <input name="phone" value="{{ old('phone') }}" class="{{ $input }}" required>
        @error('phone')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="text-sm font-extrabold text-slate-700">Date of birth</label>
        <input type="date" name="dob" value="{{ old('dob') }}" class="{{ $input }}">
        @error('dob')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="text-sm font-extrabold text-slate-700">NI number (optional)</label>
        <input name="ni_number" value="{{ old('ni_number') }}" class="{{ $input }}" placeholder="e.g. QQ 12 34 56 C">
        @error('ni_number')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div class="md:col-span-2">
        <label class="text-sm font-extrabold text-slate-700">Address</label>
        <textarea name="address" rows="3" class="{{ $textarea }}" placeholder="House no, street, town, postcode">{{ old('address') }}</textarea>
        @error('address')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>
</section>

{{-- =========================
  STEP 2 — GENERAL INFO
========================= --}}
<section x-show="step===2" x-cloak class="space-y-8">
  <div class="rounded-3xl bg-white border border-slate-200 p-6 md:p-8 shadow-[0_14px_40px_rgba(15,23,42,0.08)]">
    <h3 class="text-xl md:text-2xl font-extrabold text-slate-900">General information</h3>
    <p class="mt-2 text-slate-600">Help us understand your availability and experience.</p>

    <div class="mt-8 grid gap-5 md:grid-cols-2">
      <div>
        <label class="text-sm font-extrabold text-slate-700">Right to work in the UK? <span class="text-red-600">*</span></label>
        <select name="right_to_work" class="{{ $select }}" required>
          <option value="">Select…</option>
          <option value="yes" @selected(old('right_to_work')==='yes')>Yes</option>
          <option value="no" @selected(old('right_to_work')==='no')>No</option>
        </select>
        @error('right_to_work')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="text-sm font-extrabold text-slate-700">DBS status</label>
        <select name="dbs_status" class="{{ $select }}">
          <option value="">Select…</option>
          <option value="none" @selected(old('dbs_status')==='none')>No DBS</option>
          <option value="in_date" @selected(old('dbs_status')==='in_date')>In date DBS</option>
          <option value="on_update_service" @selected(old('dbs_status')==='on_update_service')>On Update Service</option>
          <option value="expired" @selected(old('dbs_status')==='expired')>Expired DBS</option>
        </select>
        @error('dbs_status')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="text-sm font-extrabold text-slate-700">Experience in care? <span class="text-red-600">*</span></label>
        <select name="care_experience" class="{{ $select }}" required>
          <option value="">Select…</option>
          <option value="none" @selected(old('care_experience')==='none')>No experience</option>
          <option value="lt_1" @selected(old('care_experience')==='lt_1')>Less than 1 year</option>
          <option value="1_2" @selected(old('care_experience')==='1_2')>1–2 years</option>
          <option value="3_5" @selected(old('care_experience')==='3_5')>3–5 years</option>
          <option value="5_plus" @selected(old('care_experience')==='5_plus')>5+ years</option>
        </select>
        @error('care_experience')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="text-sm font-extrabold text-slate-700">Preferred role</label>
        <select name="preferred_role" class="{{ $select }}">
          <option value="">Select…</option>
          <option value="care_assistant" @selected(old('preferred_role')==='care_assistant')>Care Assistant</option>
          <option value="senior_carer" @selected(old('preferred_role')==='senior_carer')>Senior Carer</option>
          <option value="support_worker" @selected(old('preferred_role')==='support_worker')>Support Worker</option>
        </select>
        @error('preferred_role')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="text-sm font-extrabold text-slate-700">Availability <span class="text-red-600">*</span></label>
        <select name="availability" class="{{ $select }}" required>
          <option value="">Select…</option>
          <option value="full_time" @selected(old('availability')==='full_time')>Full-time</option>
          <option value="part_time" @selected(old('availability')==='part_time')>Part-time</option>
          <option value="bank" @selected(old('availability')==='bank')>Bank / Agency</option>
          <option value="night" @selected(old('availability')==='night')>Night shifts</option>
        </select>
        @error('availability')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="text-sm font-extrabold text-slate-700">Start date</label>
        <input type="date" name="start_date" value="{{ old('start_date') }}" class="{{ $input }}">
        @error('start_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div class="md:col-span-2">
        <label class="text-sm font-extrabold text-slate-700">Why do you want this role?</label>
        <textarea name="why_role" rows="5" class="{{ $textarea }}" placeholder="A short message helps us a lot.">{{ old('why_role') }}</textarea>
        @error('why_role')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
      </div>
    </div>

    {{-- References (simple) --}}
    <div class="mt-10 rounded-3xl bg-slate-50 border border-slate-200 p-6">
      <h4 class="text-lg font-extrabold text-slate-900">References</h4>
      <p class="mt-1 text-sm text-slate-600">Please provide at least one reference.</p>

      <div class="mt-6 grid gap-5 md:grid-cols-2">
        <div>
          <label class="text-sm font-extrabold text-slate-700">Reference name <span class="text-red-600">*</span></label>
          <input name="ref1_name" value="{{ old('ref1_name') }}" class="{{ $input }}" required>
          @error('ref1_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="text-sm font-extrabold text-slate-700">Relationship</label>
          <input name="ref1_relationship" value="{{ old('ref1_relationship') }}" class="{{ $input }}" placeholder="Manager, Supervisor, Tutor...">
          @error('ref1_relationship')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="text-sm font-extrabold text-slate-700">Phone</label>
          <input name="ref1_phone" value="{{ old('ref1_phone') }}" class="{{ $input }}">
          @error('ref1_phone')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="text-sm font-extrabold text-slate-700">Email</label>
          <input type="email" name="ref1_email" value="{{ old('ref1_email') }}" class="{{ $input }}">
          @error('ref1_email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

  </div>
</section>

{{-- =========================
  STEP 3 — DECLARATION
========================= --}}
<section x-show="step===3" x-cloak class="space-y-8">
  <div class="rounded-3xl bg-white border border-slate-200 p-6 md:p-8 shadow-[0_14px_40px_rgba(15,23,42,0.08)]">
    <h3 class="text-xl md:text-2xl font-extrabold text-slate-900">Declaration</h3>
    <p class="mt-2 text-slate-600">Please confirm the statements below before submitting.</p>

    <div class="mt-8 space-y-5">
      <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-5">
        <input type="checkbox" name="declare_truth" value="1" class="mt-1" @checked(old('declare_truth')) required>
        <span class="text-sm text-slate-700">
          <span class="font-extrabold text-slate-900">I confirm</span> the information provided is true and accurate to the best of my knowledge.
        </span>
      </label>
      @error('declare_truth')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror

      <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-5">
        <input type="checkbox" name="declare_safeguarding" value="1" class="mt-1" @checked(old('declare_safeguarding')) required>
        <span class="text-sm text-slate-700">
          I understand this role involves safeguarding, and I consent to relevant checks (including DBS where applicable).
        </span>
      </label>
      @error('declare_safeguarding')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror

      <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <label class="text-sm font-extrabold text-slate-700">Signature (type your full name) <span class="text-red-600">*</span></label>
        <input name="signature" value="{{ old('signature') }}" class="{{ $input }}" required placeholder="Your full name">
        @error('signature')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror

        <p class="mt-2 text-xs text-slate-500">
          Submitting this application will create a record in the admin dashboard.
        </p>
      </div>
    </div>
  </div>
</section>
