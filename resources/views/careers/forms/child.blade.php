{{-- resources/views/careers/forms/child.blade.php --}}

{{-- STEP 1 --}}
<div x-show="step === 1" x-cloak class="space-y-8">
  <div class="rounded-2xl bg-white p-6 md:p-8 shadow-[0_10px_30px_rgba(15,23,42,0.08)]">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h3 class="text-lg md:text-xl font-extrabold text-slate-900">Personal Details</h3>
        <p class="mt-1 text-sm text-slate-600">Tell us about yourself.</p>
      </div>
      <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 border border-sky-100">
        Step 1
      </span>
    </div>

    <div class="mt-6 grid gap-5 sm:grid-cols-2">
      <div>
        <label class="block text-sm font-semibold text-slate-700">Full name *</label>
        <input name="full_name" value="{{ old('full_name') }}" class="{{ $input }}" autocomplete="name" />
        @error('full_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Email *</label>
        <input name="email" value="{{ old('email') }}" class="{{ $input }}" autocomplete="email" />
        @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Phone</label>
        <input name="phone" value="{{ old('phone') }}" class="{{ $input }}" autocomplete="tel" />
        @error('phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">National Insurance Number</label>
        <input name="ni_number" value="{{ old('ni_number') }}" class="{{ $input }}" />
        @error('ni_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">Address</label>
        <textarea name="address" rows="3" class="{{ $textarea }}">{{ old('address') }}</textarea>
        @error('address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Date of birth</label>
        <input type="date" name="dob" value="{{ old('dob') }}" class="{{ $input }}" />
        @error('dob') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Postcode</label>
        <input name="postcode" value="{{ old('postcode') }}" class="{{ $input }}" autocomplete="postal-code" />
        @error('postcode') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Nationality</label>
        <input name="nationality" value="{{ old('nationality') }}" class="{{ $input }}" />
        @error('nationality') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Right to work in the UK?</label>
        <select name="right_to_work" class="{{ $select }}">
          <option value="">Select</option>
          <option value="yes" @selected(old('right_to_work')==='yes')>Yes</option>
          <option value="no" @selected(old('right_to_work')==='no')>No</option>
        </select>
        @error('right_to_work') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div class="sm:col-span-2">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h4 class="text-sm font-extrabold text-slate-900">Next of kin</h4>
              <p class="mt-1 text-xs text-slate-600">Who should we contact in an emergency?</p>
            </div>
          </div>

          <div class="mt-4 grid gap-5 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-semibold text-slate-700">Name</label>
              <input name="next_of_kin_name" value="{{ old('next_of_kin_name') }}" class="{{ $input }}" />
              @error('next_of_kin_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700">Phone</label>
              <input name="next_of_kin_phone" value="{{ old('next_of_kin_phone') }}" class="{{ $input }}" />
              @error('next_of_kin_phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
              <label class="block text-sm font-semibold text-slate-700">Relationship</label>
              <input name="next_of_kin_relationship" value="{{ old('next_of_kin_relationship') }}" class="{{ $input }}" />
              @error('next_of_kin_relationship') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- STEP 2 --}}
<div x-show="step === 2" x-cloak class="space-y-8">
  <div class="rounded-2xl bg-white p-6 md:p-8 shadow-[0_10px_30px_rgba(15,23,42,0.08)]">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h3 class="text-lg md:text-xl font-extrabold text-slate-900">General Information</h3>
        <p class="mt-1 text-sm text-slate-600">Your experience, training and references.</p>
      </div>
      <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 border border-sky-100">
        Step 2
      </span>
    </div>

    {{-- Everything stays in the grid --}}
    <div class="mt-6 grid gap-6 sm:grid-cols-2">

      {{-- Driving licence & car --}}
      <div>
        <label class="block text-sm font-semibold text-slate-700">Do you have a UK driving licence?</label>
        <select name="has_driving_licence" class="{{ $select }}">
          <option value="">Select</option>
          <option value="yes" @selected(old('has_driving_licence')==='yes')>Yes</option>
          <option value="no" @selected(old('has_driving_licence')==='no')>No</option>
        </select>
        @error('has_driving_licence') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Do you have a car?</label>
        <select name="has_car" class="{{ $select }}">
          <option value="">Select</option>
          <option value="yes" @selected(old('has_car')==='yes')>Yes</option>
          <option value="no" @selected(old('has_car')==='no')>No</option>
        </select>
        @error('has_car') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      {{-- EDUCATION --}}
      <div class="sm:col-span-2">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h4 class="text-sm font-extrabold text-slate-900">Education & Qualifications</h4>
              <p class="mt-1 text-xs text-slate-600">Add your most relevant qualifications (you can add multiple).</p>
            </div>
            <button type="button"
              @click="education.push({ qualification:'', institution:'', year:'' })"
              class="inline-flex items-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-800 border border-slate-200 hover:bg-slate-100">
              + Add
            </button>
          </div>

          <div class="mt-4 space-y-4">
            <template x-for="(row, i) in education" :key="i">
              <div class="rounded-xl bg-white border border-slate-200 p-4">
                <div class="flex items-start justify-between gap-3">
                  <p class="text-sm font-bold text-slate-800">Qualification <span x-text="i + 1"></span></p>

                  <button type="button"
                    @click="education.splice(i, 1)"
                    class="text-sm font-semibold text-red-600 hover:underline"
                    x-show="education.length > 1">
                    Remove
                  </button>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Qualification</label>
                    <input :name="`education[${i}][qualification]`" x-model="row.qualification" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Institution</label>
                    <input :name="`education[${i}][institution]`" x-model="row.institution" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Year</label>
                    <input :name="`education[${i}][year]`" x-model="row.year" class="{{ $input }}" placeholder="e.g. 2024" />
                  </div>
                </div>
              </div>
            </template>
          </div>

          @error('education') <p class="mt-3 text-sm text-red-600">{{ $message }}</p> @enderror
          @error('education.*.*') <p class="mt-3 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- EMPLOYMENT --}}
      <div class="sm:col-span-2">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h4 class="text-sm font-extrabold text-slate-900">Employment History</h4>
              <p class="mt-1 text-xs text-slate-600">Add your recent jobs (you can add multiple).</p>
            </div>
            <button type="button"
              @click="employment.push({ employer:'', role:'', from:'', to:'', reason:'' })"
              class="inline-flex items-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-800 border border-slate-200 hover:bg-slate-100">
              + Add
            </button>
          </div>

          <div class="mt-4 space-y-4">
            <template x-for="(row, i) in employment" :key="i">
              <div class="rounded-xl bg-white border border-slate-200 p-4">
                <div class="flex items-start justify-between gap-3">
                  <p class="text-sm font-bold text-slate-800">Job <span x-text="i + 1"></span></p>
                  <button type="button"
                    @click="employment.splice(i, 1)"
                    class="text-sm font-semibold text-red-600 hover:underline"
                    x-show="employment.length > 1">
                    Remove
                  </button>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Employer</label>
                    <input :name="`employment[${i}][employer]`" x-model="row.employer" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Role / Position</label>
                    <input :name="`employment[${i}][role]`" x-model="row.role" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">From</label>
                    <input type="month" :name="`employment[${i}][from]`" x-model="row.from" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">To</label>
                    <input type="month" :name="`employment[${i}][to]`" x-model="row.to" class="{{ $input }}" />
                  </div>
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700">Reason for leaving</label>
                    <textarea rows="3" :name="`employment[${i}][reason]`" x-model="row.reason" class="{{ $textarea }}"></textarea>
                  </div>
                </div>
              </div>
            </template>
          </div>

          @error('employment') <p class="mt-3 text-sm text-red-600">{{ $message }}</p> @enderror
          @error('employment.*.*') <p class="mt-3 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- VOLUNTARY WORK --}}
      <div class="sm:col-span-2">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h4 class="text-sm font-extrabold text-slate-900">Voluntary Work</h4>
              <p class="mt-1 text-xs text-slate-600">Add any voluntary roles (optional).</p>
            </div>
            <button type="button"
              @click="voluntary.push({ organisation:'', role:'', from:'', to:'', hours:'', reason:'' })"
              class="inline-flex items-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-800 border border-slate-200 hover:bg-slate-100">
              + Add
            </button>
          </div>

          <div class="mt-4 space-y-4">
            <template x-for="(row, i) in voluntary" :key="i">
              <div class="rounded-xl bg-white border border-slate-200 p-4">
                <div class="flex items-start justify-between gap-3">
                  <p class="text-sm font-bold text-slate-800">Voluntary <span x-text="i + 1"></span></p>
                  <button type="button"
                    @click="voluntary.splice(i, 1)"
                    class="text-sm font-semibold text-red-600 hover:underline"
                    x-show="voluntary.length > 1">
                    Remove
                  </button>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Organisation</label>
                    <input :name="`voluntary[${i}][organisation]`" x-model="row.organisation" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Role / Duties</label>
                    <input :name="`voluntary[${i}][role]`" x-model="row.role" class="{{ $input }}" />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-slate-700">From</label>
                    <input type="month" :name="`voluntary[${i}][from]`" x-model="row.from" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">To</label>
                    <input type="month" :name="`voluntary[${i}][to]`" x-model="row.to" class="{{ $input }}" />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Hours per week</label>
                    <input :name="`voluntary[${i}][hours]`" x-model="row.hours" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Reason for leaving</label>
                    <input :name="`voluntary[${i}][reason]`" x-model="row.reason" class="{{ $input }}" />
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>

      {{-- OTHER EXPERIENCE --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">Other experience since leaving full-time education</label>
        <textarea name="other_experience" rows="4" class="{{ $textarea }}">{{ old('other_experience') }}</textarea>
      </div>

      {{-- OTHER INTERESTS --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">Other interests / hobbies / achievements</label>
        <textarea name="interests" rows="4" class="{{ $textarea }}">{{ old('interests') }}</textarea>
      </div>

      {{-- TRAINING --}}
      <div class="sm:col-span-2">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h4 class="text-sm font-extrabold text-slate-900">Mandatory Training</h4>
              <p class="mt-1 text-xs text-slate-600">List relevant mandatory training (you can add multiple).</p>
            </div>
            <button type="button"
              @click="training.push({ name:'', provider:'', date:'', expires:'', certificate:'' })"
              class="inline-flex items-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-800 border border-slate-200 hover:bg-slate-100">
              + Add
            </button>
          </div>

          <div class="mt-4 space-y-4">
            <template x-for="(row, i) in training" :key="i">
              <div class="rounded-xl bg-white border border-slate-200 p-4">
                <div class="flex items-start justify-between gap-3">
                  <p class="text-sm font-bold text-slate-800">Training <span x-text="i + 1"></span></p>
                  <button type="button"
                    @click="training.splice(i, 1)"
                    class="text-sm font-semibold text-red-600 hover:underline"
                    x-show="training.length > 1">
                    Remove
                  </button>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700">Training name</label>
                    <input :name="`training[${i}][name]`" x-model="row.name" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Provider</label>
                    <input :name="`training[${i}][provider]`" x-model="row.provider" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Certificate / Ref (optional)</label>
                    <input :name="`training[${i}][certificate]`" x-model="row.certificate" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Date completed</label>
                    <input type="date" :name="`training[${i}][date]`" x-model="row.date" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Expiry date (if any)</label>
                    <input type="date" :name="`training[${i}][expires]`" x-model="row.expires" class="{{ $input }}" />
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>

      {{-- DISCIPLINARY --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">Disciplinary history (if any)</label>
        <textarea name="disciplinary" rows="4" class="{{ $textarea }}">{{ old('disciplinary') }}</textarea>
      </div>

      {{-- REFERENCES --}}
      <div class="sm:col-span-2">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
          <h4 class="text-sm font-extrabold text-slate-900">Professional References</h4>
          <p class="mt-1 text-xs text-slate-600">Please provide two professional references.</p>

          @php $refs = [0 => 'Reference 1', 1 => 'Reference 2']; @endphp

          <div class="mt-4 space-y-4">
            @foreach($refs as $i => $label)
              <div class="rounded-xl bg-white border border-slate-200 p-4">
                <p class="text-sm font-bold text-slate-800">{{ $label }}</p>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Name</label>
                    <input name="references[{{ $i }}][name]" value="{{ old("references.$i.name") }}" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Position</label>
                    <input name="references[{{ $i }}][position]" value="{{ old("references.$i.position") }}" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Company</label>
                    <input name="references[{{ $i }}][company]" value="{{ old("references.$i.company") }}" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Telephone</label>
                    <input name="references[{{ $i }}][telephone]" value="{{ old("references.$i.telephone") }}" class="{{ $input }}" />
                  </div>
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700">Address</label>
                    <input name="references[{{ $i }}][address]" value="{{ old("references.$i.address") }}" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Postcode</label>
                    <input name="references[{{ $i }}][postcode]" value="{{ old("references.$i.postcode") }}" class="{{ $input }}" />
                  </div>
                  <div>
                    <label class="block text-sm font-semibold text-slate-700">Email address</label>
                    <input name="references[{{ $i }}][email]" value="{{ old("references.$i.email") }}" class="{{ $input }}" />
                  </div>
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700">Length of time known (MM/YY)</label>
                    <input name="references[{{ $i }}][known]" value="{{ old("references.$i.known") }}" class="{{ $input }}" placeholder="e.g. 06/21" />
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="mt-4">
            <label class="block text-sm font-semibold text-slate-700">May we contact your referees before your interview?</label>
            <select name="refs_contact_before_interview" class="{{ $select }}">
              <option value="">Select</option>
              <option value="yes" @selected(old('refs_contact_before_interview')==='yes')>Yes</option>
              <option value="no"  @selected(old('refs_contact_before_interview')==='no')>No</option>
            </select>
          </div>
        </div>
      </div>

      {{-- DBS --}}
      <div class="sm:col-span-2">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
          <h4 class="text-sm font-extrabold text-slate-900">Disclosures & DBS</h4>
          <p class="mt-1 text-xs text-slate-600">These questions are required for regulated roles.</p>

          <div class="mt-4 grid gap-5 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-semibold text-slate-700">Any spent or unspent convictions (UK or overseas)?</label>
              <select name="convictions" class="{{ $select }}">
                <option value="">Select</option>
                <option value="yes" @selected(old('convictions')==='yes')>Yes</option>
                <option value="no"  @selected(old('convictions')==='no')>No</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700">Any investigation alleging abuse (UK or overseas)?</label>
              <select name="abuse_investigation" class="{{ $select }}">
                <option value="">Select</option>
                <option value="yes" @selected(old('abuse_investigation')==='yes')>Yes</option>
                <option value="no"  @selected(old('abuse_investigation')==='no')>No</option>
              </select>
            </div>

            <div class="sm:col-span-2">
              <label class="block text-sm font-semibold text-slate-700">If yes, please give details</label>
              <textarea name="disclosure_details" rows="4" class="{{ $textarea }}">{{ old('disclosure_details') }}</textarea>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700">Do you agree to apply for Enhanced DBS checks?</label>
              <select name="agree_enhanced_dbs" class="{{ $select }}">
                <option value="">Select</option>
                <option value="yes" @selected(old('agree_enhanced_dbs')==='yes')>Yes</option>
                <option value="no"  @selected(old('agree_enhanced_dbs')==='no')>No</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700">Any pending investigation?</label>
              <select name="pending_investigation" class="{{ $select }}">
                <option value="">Select</option>
                <option value="yes" @selected(old('pending_investigation')==='yes')>Yes</option>
                <option value="no"  @selected(old('pending_investigation')==='no')>No</option>
              </select>
            </div>

            <div class="sm:col-span-2">
              <label class="block text-sm font-semibold text-slate-700">Pending investigation details (if any)</label>
              <textarea name="pending_investigation_details" rows="3" class="{{ $textarea }}">{{ old('pending_investigation_details') }}</textarea>
            </div>

            <div class="sm:col-span-2">
              <label class="inline-flex items-start gap-3">
                <input type="checkbox" name="authorise_dbs" value="1" class="mt-1 rounded border-slate-300"
                      @checked(old('authorise_dbs')) />
                <span class="text-sm text-slate-700">
                  I authorise Gims Care Solutions to carry out a DBS check on my behalf.
                </span>
              </label>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- STEP 3 --}}
<div x-show="step === 3" x-cloak class="space-y-8">
  <div class="rounded-2xl bg-white p-6 md:p-8 shadow-[0_10px_30px_rgba(15,23,42,0.08)]">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h3 class="text-lg md:text-xl font-extrabold text-slate-900">Final Step</h3>
        <p class="mt-1 text-sm text-slate-600">Availability, skills, equal opportunities and declaration.</p>
      </div>
      <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 border border-sky-100">
        Step 3
      </span>
    </div>

    {{-- Confidentiality --}}
    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
      <h4 class="text-sm font-extrabold text-slate-900">Confidentiality</h4>

      <label class="mt-3 inline-flex items-start gap-3">
        <input type="checkbox" name="confidentiality_agree" value="1" class="mt-1 rounded border-slate-300"
              @checked(old('confidentiality_agree')) />
        <span class="text-sm text-slate-700">
          I have read and understand the confidentiality requirements and agree.
        </span>
      </label>

      <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-semibold text-slate-700">Signature (type full name)</label>
          <input name="confidentiality_signature" value="{{ old('confidentiality_signature') }}" class="{{ $input }}" />
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700">Date</label>
          <input type="date" name="confidentiality_date" value="{{ old('confidentiality_date') }}" class="{{ $input }}" />
        </div>
      </div>
    </div>

    {{-- Working Time --}}
    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
      <h4 class="text-sm font-extrabold text-slate-900">Working Time Directive</h4>

      <div class="mt-3">
        <label class="block text-sm font-semibold text-slate-700">Do you wish to opt-out of the 48-hour weekly limit?</label>
        <select name="working_time_opt_out" class="{{ $select }}">
          <option value="">Select</option>
          <option value="yes" @selected(old('working_time_opt_out')==='yes')>Yes</option>
          <option value="no"  @selected(old('working_time_opt_out')==='no')>No</option>
        </select>
      </div>

      <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-semibold text-slate-700">Signature</label>
          <input name="working_time_signature" value="{{ old('working_time_signature') }}" class="{{ $input }}" />
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700">Date</label>
          <input type="date" name="working_time_date" value="{{ old('working_time_date') }}" class="{{ $input }}" />
        </div>
      </div>
    </div>

    {{-- Availability --}}
    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
      <h4 class="text-sm font-extrabold text-slate-900">Availability</h4>
      <p class="mt-1 text-xs text-slate-600">Tick when you’re available. Add notes if needed.</p>

      @php
        $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $slots = ['morning' => 'Morning', 'afternoon' => 'Afternoon', 'evening' => 'Evening', 'night' => 'Night'];
      @endphp

      <div class="mt-4 overflow-x-auto rounded-xl bg-white border border-slate-200">
        <table class="min-w-[760px] w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-bold text-slate-700">Day</th>
              @foreach($slots as $key => $label)
                <th class="px-4 py-3 text-center font-bold text-slate-700">{{ $label }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($days as $d)
              <tr class="border-t border-slate-200">
                <td class="px-4 py-3 font-semibold text-slate-700">{{ $d }}</td>
                @foreach($slots as $key => $label)
                  <td class="px-4 py-3 text-center">
                    <input
                      type="checkbox"
                      class="rounded border-slate-300"
                      name="availability[{{ strtolower($d) }}][{{ $key }}]"
                      value="1"
                      @checked(old('availability.' . strtolower($d) . '.' . $key))
                    />
                  </td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-semibold text-slate-700">Preferred hours / constraints (optional)</label>
          <textarea name="availability_notes" rows="3" class="{{ $textarea }}">{{ old('availability_notes') }}</textarea>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700">Earliest start date</label>
          <input type="date" name="available_from" value="{{ old('available_from') }}" class="{{ $input }}" />
        </div>
      </div>
    </div>

    {{-- Bank --}}
    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
      <h4 class="text-sm font-extrabold text-slate-900">Bank Details</h4>

      <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-semibold text-slate-700">Account holder name</label>
          <input name="bank_account_holder" value="{{ old('bank_account_holder') }}" class="{{ $input }}" />
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700">Bank name</label>
          <input name="bank_name" value="{{ old('bank_name') }}" class="{{ $input }}" />
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700">Account number</label>
          <input name="bank_account_number" value="{{ old('bank_account_number') }}" class="{{ $input }}" />
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700">Sort code</label>
          <input name="bank_sort_code" value="{{ old('bank_sort_code') }}" class="{{ $input }}" />
        </div>
      </div>

      <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-semibold text-slate-700">Signature</label>
          <input name="bank_signature" value="{{ old('bank_signature') }}" class="{{ $input }}" />
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-700">Date</label>
          <input type="date" name="bank_date" value="{{ old('bank_date') }}" class="{{ $input }}" />
        </div>
      </div>
    </div>

    {{-- Skills --}}
    @php
      $skills = [
        'Personal Hygiene' => [
          'assisting_washing_shaving' => 'Assisting with washing, shaving',
          'eye_care' => 'Eye care',
          'mouth_care' => 'Mouth care',
          'stoma_care' => 'Stoma care',
        ],
        'Toileting' => [
          'continence_care' => 'Continence care',
          'catheter_care' => 'Catheter care',
          'commode_bedpan' => 'Use of commode / bed pan',
          'fluid_balance' => 'Recording fluid balance sheets',
        ],
        'Observations' => [
          'blood_sugar' => 'Blood sugar testing',
          'bp_recording' => 'B/P recording',
          'tpr_recording' => 'TPR recording',
          'urinalysis' => 'Urinalysis',
        ],
        'Nutrition' => [
          'feeding_patient' => 'Feeding patient',
          'meal_preparation' => 'Meal preparation',
        ],
        'Mobility' => [
          'use_of_hoist' => 'Use of hoist',
          'transfer_mobilise' => 'Transferring / mobilising a patient',
        ],
        'Other' => [
          'report_writing' => 'Report writing',
          'pressure_area_care' => 'Pressure area care',
        ],
      ];
    @endphp

    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
      <h4 class="text-sm font-extrabold text-slate-900">Skills Evaluation</h4>
      <p class="mt-1 text-xs text-slate-600">A = experienced, B = familiar, C = no knowledge.</p>

      <div class="mt-4 space-y-6">
        @foreach($skills as $group => $items)
          <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-sm font-bold text-slate-800">{{ $group }}</p>

            <div class="mt-4 space-y-3">
              @foreach($items as $key => $label)
                <div class="grid gap-3 sm:grid-cols-6 sm:items-center">
                  <div class="sm:col-span-3 text-sm text-slate-700 font-semibold">{{ $label }}</div>

                  <div class="sm:col-span-1">
                    <select name="skills[{{ $key }}][level]" class="{{ $select }}">
                      <option value="">-</option>
                      <option value="A" @selected(old("skills.$key.level")==='A')>A</option>
                      <option value="B" @selected(old("skills.$key.level")==='B')>B</option>
                      <option value="C" @selected(old("skills.$key.level")==='C')>C</option>
                    </select>
                  </div>

                  <div class="sm:col-span-2">
                    <input
                      name="skills[{{ $key }}][comments]"
                      value="{{ old("skills.$key.comments") }}"
                      class="{{ $input }}"
                      placeholder="Comments (optional)"
                    />
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Equal Opportunities --}}
    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
      <h4 class="text-sm font-extrabold text-slate-900">Equal Opportunities (Optional)</h4>
      <p class="mt-1 text-xs text-slate-600">Used for monitoring only. You can choose “Prefer not to say”.</p>

      <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-semibold text-slate-700">Gender</label>
          <select name="equal[gender]" class="{{ $select }}">
            <option value="">Prefer not to say</option>
            <option value="female" @selected(old('equal.gender')==='female')>Female</option>
            <option value="male" @selected(old('equal.gender')==='male')>Male</option>
            <option value="other" @selected(old('equal.gender')==='other')>Other</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700">Age band</label>
          <select name="equal[age_band]" class="{{ $select }}">
            <option value="">Prefer not to say</option>
            <option value="16-24" @selected(old('equal.age_band')==='16-24')>16–24</option>
            <option value="25-34" @selected(old('equal.age_band')==='25-34')>25–34</option>
            <option value="35-44" @selected(old('equal.age_band')==='35-44')>35–44</option>
            <option value="45-54" @selected(old('equal.age_band')==='45-54')>45–54</option>
            <option value="55-64" @selected(old('equal.age_band')==='55-64')>55–64</option>
            <option value="65+" @selected(old('equal.age_band')==='65+')>65+</option>
          </select>
        </div>

        <div class="sm:col-span-2">
          <label class="block text-sm font-semibold text-slate-700">Ethnicity</label>
          <input name="equal[ethnicity]" value="{{ old('equal.ethnicity') }}" class="{{ $input }}" placeholder="Prefer not to say" />
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700">Religion / belief</label>
          <input name="equal[religion]" value="{{ old('equal.religion') }}" class="{{ $input }}" placeholder="Prefer not to say" />
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700">Disability</label>
          <select name="equal[disability]" class="{{ $select }}">
            <option value="">Prefer not to say</option>
            <option value="no" @selected(old('equal.disability')==='no')>No</option>
            <option value="yes" @selected(old('equal.disability')==='yes')>Yes</option>
          </select>
        </div>

        <div class="sm:col-span-2">
          <label class="block text-sm font-semibold text-slate-700">If yes, any adjustments needed? (optional)</label>
          <textarea name="equal[adjustments]" rows="3" class="{{ $textarea }}">{{ old('equal.adjustments') }}</textarea>
        </div>
      </div>
    </div>

    {{-- Declaration --}}
    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 md:p-5">
      <h4 class="text-sm font-extrabold text-slate-900">Declaration</h4>

      <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-semibold text-slate-700">Signature (type your full name) *</label>
          <input name="declaration_signature" value="{{ old('declaration_signature') }}" class="{{ $input }}" />
          @error('declaration_signature') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700">Date *</label>
          <input type="date" name="declaration_date" value="{{ old('declaration_date') }}" class="{{ $input }}" />
          @error('declaration_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
      </div>

      <p class="mt-4 text-sm text-slate-600">
        By submitting, you confirm the information provided is accurate to the best of your knowledge.
      </p>
    </div>

  </div>
</div>