@extends('layouts.site')

@section('content')
<style>
    .giving-amount-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .giving-amount-option {
        min-height: 3.75rem;
        border: 1px solid #cbd5e1;
        border-radius: 1rem;
        background: #ffffff;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
        transition: all 0.18s ease;
    }

    .giving-amount-option:hover {
        border-color: #2563eb;
        background: #eff6ff;
    }

    .giving-amount-option.is-selected {
        border-color: #1d4ed8;
        background: #1d4ed8;
        color: #ffffff;
        box-shadow: 0 8px 20px rgba(29, 78, 216, 0.18);
    }

    .giving-custom-amount {
        display: flex;
        align-items: stretch;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        border-radius: 1rem;
        background: #ffffff;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
    }

    .giving-custom-amount:focus-within {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .giving-custom-amount-prefix {
        display: flex;
        min-width: 3.25rem;
        align-items: center;
        justify-content: center;
        border-right: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 1.25rem;
        font-weight: 800;
    }

    .giving-custom-amount-input {
        width: 100%;
        min-width: 0;
        border: 0;
        padding: 1rem;
        background: transparent;
        color: #0f172a;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.25;
        outline: none;
    }

    @media (min-width: 640px) {
        .giving-amount-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }
</style>

<section class="relative overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(0,74,173,0.35),transparent_38%),radial-gradient(circle_at_bottom_right,rgba(37,99,235,0.24),transparent_42%)]"></div>

    <div class="relative mx-auto max-w-6xl px-4 py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-3xl text-center">
            <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-extrabold uppercase tracking-[0.2em] text-blue-200">
                Secure Online Giving
            </span>

            <h1 class="mt-6 text-4xl font-extrabold tracking-tight sm:text-5xl">
                Partner with us through your giving
            </h1>

            <p class="mx-auto mt-5 max-w-2xl text-base leading-8 text-white/70 sm:text-lg">
                Your generosity helps us advance ministry, support people in need, and serve our community.
            </p>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-12 sm:py-16">
    <div class="mx-auto max-w-4xl px-4">
        @if(session('giving_error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                {{ session('giving_error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-red-700">
                <div class="font-extrabold">Please check the form and try again.</div>
                <ul class="mt-3 list-disc space-y-1 pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('giving.checkout') }}"
            class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/60"
        >
            @csrf

            <div class="border-b border-slate-200 px-6 py-6 sm:px-8">
                <h2 class="text-2xl font-extrabold text-slate-950">Make a donation</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    You will be redirected to Stripe to complete your payment securely.
                </p>
            </div>

            <div class="space-y-8 px-6 py-8 sm:px-8">
                <div>
                    <label class="block text-sm font-extrabold text-slate-900">
                        Choose an amount
                    </label>

                    <div class="giving-amount-grid mt-4">
                        @foreach([10, 25, 50, 100] as $preset)
                            <button
                                type="button"
                                data-amount="{{ $preset }}"
                                class="giving-amount-option"
                            >
                                £{{ number_format($preset, 0) }}
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-5">
                        <label for="amount" class="block text-sm font-bold text-slate-700">
                            Or enter another amount
                        </label>

                        <div class="giving-custom-amount mt-2">
                            <span class="giving-custom-amount-prefix">£</span>

                            <input
                                id="amount"
                                name="amount"
                                type="number"
                                min="1"
                                max="999999.99"
                                step="0.01"
                                inputmode="decimal"
                                value="{{ old('amount') }}"
                                required
                                class="giving-custom-amount-input"
                                placeholder="0.00"
                            >
                        </div>
                    </div>
                </div>

                <div>
                    <label for="donation_fund_id" class="block text-sm font-extrabold text-slate-900">
                        Donation fund
                    </label>

                    <select
                        id="donation_fund_id"
                        name="donation_fund_id"
                        required
                        class="mt-2 block w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-base font-semibold text-slate-950 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                    >
                        <option value="">Select a fund</option>

                        @foreach($funds as $fund)
                            <option
                                value="{{ $fund->id }}"
                                @selected(
                                    (string) old('donation_fund_id') === (string) $fund->id
                                    || (
                                        old('donation_fund_id') === null
                                        && $fund->is_default
                                    )
                                )
                            >
                                {{ $fund->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="donor_name" class="block text-sm font-extrabold text-slate-900">
                            Full name
                        </label>

                        <input
                            id="donor_name"
                            name="donor_name"
                            type="text"
                            value="{{ old('donor_name') }}"
                            class="mt-2 block w-full rounded-2xl border border-slate-300 px-4 py-4 text-base text-slate-950 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            placeholder="Your full name"
                        >
                    </div>

                    <div>
                        <label for="donor_email" class="block text-sm font-extrabold text-slate-900">
                            Email address
                        </label>

                        <input
                            id="donor_email"
                            name="donor_email"
                            type="email"
                            value="{{ old('donor_email') }}"
                            required
                            class="mt-2 block w-full rounded-2xl border border-slate-300 px-4 py-4 text-base text-slate-950 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            placeholder="you@example.com"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="donor_phone" class="block text-sm font-extrabold text-slate-900">
                            Phone number
                            <span class="font-medium text-slate-500">(optional)</span>
                        </label>

                        <input
                            id="donor_phone"
                            name="donor_phone"
                            type="tel"
                            value="{{ old('donor_phone') }}"
                            class="mt-2 block w-full rounded-2xl border border-slate-300 px-4 py-4 text-base text-slate-950 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            placeholder="Your phone number"
                        >
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4">
                        <input
                            id="is_anonymous"
                            name="is_anonymous"
                            type="checkbox"
                            value="1"
                            @checked(old('is_anonymous'))
                            class="mt-1 h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-600"
                        >

                        <span>
                            <span class="block font-extrabold text-slate-900">Give anonymously</span>
                            <span class="mt-1 block text-sm leading-6 text-slate-600">
                                Your name will not appear in donor-facing records.
                            </span>
                        </span>
                    </label>

                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4">
                        <input
                            id="gift_aid"
                            name="gift_aid"
                            type="checkbox"
                            value="1"
                            @checked(old('gift_aid'))
                            class="mt-1 h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-600"
                        >

                        <span>
                            <span class="block font-extrabold text-slate-900">Add Gift Aid</span>
                            <span class="mt-1 block text-sm leading-6 text-slate-600">
                                I am a UK taxpayer and understand that Gift Aid may be reclaimed on this donation.
                            </span>
                        </span>
                    </label>
                </div>

                <div
                    id="gift-aid-fields"
                    class="hidden rounded-3xl border border-blue-100 bg-blue-50/60 p-5 sm:p-6"
                >
                    <h3 class="text-lg font-extrabold text-slate-950">Gift Aid address</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Please provide your home address for Gift Aid records.
                    </p>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="address_line_1" class="block text-sm font-bold text-slate-800">
                                Address line 1
                            </label>
                            <input
                                id="address_line_1"
                                name="address_line_1"
                                type="text"
                                value="{{ old('address_line_1') }}"
                                class="mt-2 block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3.5 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            >
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address_line_2" class="block text-sm font-bold text-slate-800">
                                Address line 2
                                <span class="font-medium text-slate-500">(optional)</span>
                            </label>
                            <input
                                id="address_line_2"
                                name="address_line_2"
                                type="text"
                                value="{{ old('address_line_2') }}"
                                class="mt-2 block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3.5 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            >
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-bold text-slate-800">Town or city</label>
                            <input
                                id="city"
                                name="city"
                                type="text"
                                value="{{ old('city') }}"
                                class="mt-2 block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3.5 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            >
                        </div>

                        <div>
                            <label for="county" class="block text-sm font-bold text-slate-800">County</label>
                            <input
                                id="county"
                                name="county"
                                type="text"
                                value="{{ old('county') }}"
                                class="mt-2 block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3.5 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            >
                        </div>

                        <div>
                            <label for="postcode" class="block text-sm font-bold text-slate-800">Postcode</label>
                            <input
                                id="postcode"
                                name="postcode"
                                type="text"
                                value="{{ old('postcode') }}"
                                class="mt-2 block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3.5 uppercase outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            >
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-bold text-slate-800">Country</label>
                            <input
                                id="country"
                                name="country"
                                type="text"
                                value="{{ old('country', 'United Kingdom') }}"
                                class="mt-2 block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3.5 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20"
                            >
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-700 px-6 py-4 text-base font-extrabold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-600/20"
                >
                    Give securely with Stripe
                </button>

                <div class="flex items-center justify-center gap-2 text-center text-xs font-semibold text-slate-500">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M7 10V8a5 5 0 0 1 10 0v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                    Secure payment processing provided by Stripe
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const amountInput = document.getElementById('amount');
        const amountButtons = document.querySelectorAll('.giving-amount-option');
        const giftAidCheckbox = document.getElementById('gift_aid');
        const giftAidFields = document.getElementById('gift-aid-fields');
        const anonymousCheckbox = document.getElementById('is_anonymous');
        const donorName = document.getElementById('donor_name');

        function updateAmountButtons() {
            amountButtons.forEach(function (button) {
                const selected = Number(amountInput.value) === Number(button.dataset.amount);
                button.classList.toggle('is-selected', selected);
            });
        }

        function updateGiftAidFields() {
            const enabled = giftAidCheckbox.checked;

            giftAidFields.classList.toggle('hidden', !enabled);

            ['address_line_1', 'city', 'postcode', 'country'].forEach(function (id) {
                const field = document.getElementById(id);

                if (field) {
                    field.required = enabled;
                }
            });
        }

        function updateAnonymousState() {
            donorName.disabled = anonymousCheckbox.checked;

            if (anonymousCheckbox.checked) {
                donorName.value = '';
                donorName.classList.add('bg-slate-100');
            } else {
                donorName.classList.remove('bg-slate-100');
            }
        }

        amountButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                amountInput.value = button.dataset.amount;
                updateAmountButtons();
            });
        });

        amountInput.addEventListener('input', updateAmountButtons);
        giftAidCheckbox.addEventListener('change', updateGiftAidFields);
        anonymousCheckbox.addEventListener('change', updateAnonymousState);

        updateAmountButtons();
        updateGiftAidFields();
        updateAnonymousState();
    });
</script>
@endpush