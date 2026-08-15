@extends('layouts.site')

@section('title', 'Church Transport | Fresh Fountain')

@section('meta_description', 'Book your Fresh Fountain church transport pickup for upcoming services and events.')

@section('og_title', 'Church Transport | Fresh Fountain')

@section('og_description', 'Need a ride to church? Book your Fresh Fountain pickup for upcoming services and events.')

@section('og_url', url('/transport'))

@section('og_image', asset('images/transport-share.jpg'))

@section('content')
@php($title = 'Book Church Pickup | Fresh Fountain')

<section class="bg-slate-950 text-white">
    <div class="mx-auto max-w-[1000px] px-4 py-12 sm:px-6 lg:px-8">
        <a href="{{ route('transport.index') }}" class="text-sm font-extrabold text-blue-300">← All pickup dates</a>
        <h1 class="mt-5 text-3xl font-extrabold md:text-5xl">{{ $pickupEvent->title }}</h1>
        <p class="mt-3 text-lg text-white/75">{{ $pickupEvent->pickup_date->format('l, j F Y') }}</p>
    </div>
</section>

<section class="bg-slate-50">
    <div class="mx-auto max-w-[1000px] px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <form method="POST" action="{{ route('transport.store', $pickupEvent) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_16px_45px_rgba(15,23,42,0.08)] md:p-9">
            @csrf

            @if($errors->any())
                <div class="mb-7 rounded-2xl border border-red-200 bg-red-50 p-5 text-red-800">
                    <div class="font-extrabold">Please check your booking details.</div>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-extrabold text-slate-900">Your name</label>
                    <input id="name" name="name" value="{{ old('name') }}" required maxlength="191"
                           class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-base focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-extrabold text-slate-900">Mobile number</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required maxlength="191"
                           class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-base focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-extrabold text-slate-900">Pickup address</label>
                    <textarea id="address" name="address" required maxlength="500" rows="3"
                              placeholder="House number, street, area and postcode"
                              class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-base focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
                    <p class="mt-2 text-sm text-slate-500">Please include your postcode where possible so the driver can navigate accurately.</p>
                </div>

                <div>
                    <label for="pickup_time" class="block text-sm font-extrabold text-slate-900">Pickup time</label>
                    <select id="pickup_time" name="pickup_time" required
                            class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-base focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Choose a time</option>
                        @foreach($slots as $slot)
                            <option value="{{ $slot['value'] }}"
                                    data-remaining="{{ $slot['remaining'] }}"
                                    @selected(old('pickup_time') === $slot['value'])
                                    @disabled($slot['full'])>
                                {{ $slot['label'] }} — {{ $slot['full'] ? 'FULL' : $slot['remaining'] . ' seat(s) left' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="party_size" class="block text-sm font-extrabold text-slate-900">How many people?</label>
                    <input id="party_size" name="party_size" type="number" min="1" max="{{ $maxPartySize }}" value="{{ old('party_size', 1) }}" required
                           class="mt-2 w-full rounded-2xl border border-slate-300 px-4 py-3 text-base focus:border-blue-500 focus:ring-blue-500">
                    <p id="seat-help" class="mt-2 text-sm text-slate-500">Includes you and anyone travelling with you.</p>
                </div>
            </div>

            <div class="mt-8 rounded-2xl bg-blue-50 p-5 text-sm leading-6 text-slate-700">
                The transport team will see your name, phone number, pickup address, chosen time and party size so they can organise the route.
            </div>

            <button type="submit" class="mt-8 inline-flex w-full items-center justify-center rounded-2xl bg-[rgb(var(--brand))] px-6 py-4 text-lg font-extrabold text-white transition hover:bg-[rgb(var(--brand-dark))]">
                Confirm my pickup
            </button>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const time = document.getElementById('pickup_time');
    const party = document.getElementById('party_size');
    const help = document.getElementById('seat-help');
    const configuredMax = {{ (int) $maxPartySize }};

    function updateCapacity() {
        const option = time.options[time.selectedIndex];
        const remaining = Number(option?.dataset?.remaining || configuredMax);
        const max = Math.max(1, Math.min(configuredMax, remaining));

        party.max = String(max);
        if (Number(party.value) > max) party.value = String(max);

        help.textContent = time.value
            ? `${remaining} seat(s) currently remain at this pickup time.`
            : 'Includes you and anyone travelling with you.';
    }

    time.addEventListener('change', updateCapacity);
    updateCapacity();
});
</script>
@endsection
