@extends('layouts.site')

@section('content')
@php($title = 'Pickup Confirmed | Fresh Fountain')

<section class="bg-slate-50">
    <div class="mx-auto max-w-[850px] px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="overflow-hidden rounded-3xl border border-emerald-200 bg-white shadow-[0_18px_55px_rgba(15,23,42,0.10)]">
            <div class="bg-emerald-600 px-6 py-10 text-center text-white md:px-10">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white/20 text-3xl">✓</div>
                <h1 class="mt-5 text-3xl font-extrabold md:text-4xl">Your pickup is booked</h1>
                <p class="mt-2 text-white/85">Your details are now available to the Fresh Fountain transport team.</p>
            </div>

            <div class="p-6 md:p-10">
                <dl class="divide-y divide-slate-200 rounded-2xl border border-slate-200">
                    <div class="grid gap-1 p-4 sm:grid-cols-[180px_1fr]">
                        <dt class="font-bold text-slate-500">Service</dt>
                        <dd class="font-extrabold text-slate-950">{{ $booking->pickupEvent->title }}</dd>
                    </div>
                    <div class="grid gap-1 p-4 sm:grid-cols-[180px_1fr]">
                        <dt class="font-bold text-slate-500">Date</dt>
                        <dd class="font-extrabold text-slate-950">{{ $booking->pickupEvent->pickup_date->format('l, j F Y') }}</dd>
                    </div>
                    <div class="grid gap-1 p-4 sm:grid-cols-[180px_1fr]">
                        <dt class="font-bold text-slate-500">Pickup time</dt>
                        <dd class="font-extrabold text-slate-950">{{ \Carbon\Carbon::parse($booking->pickup_time)->format('g:i A') }}</dd>
                    </div>
                    <div class="grid gap-1 p-4 sm:grid-cols-[180px_1fr]">
                        <dt class="font-bold text-slate-500">Passengers</dt>
                        <dd class="font-extrabold text-slate-950">{{ $booking->party_size }}</dd>
                    </div>
                    <div class="grid gap-1 p-4 sm:grid-cols-[180px_1fr]">
                        <dt class="font-bold text-slate-500">Pickup address</dt>
                        <dd class="font-extrabold text-slate-950">{{ $booking->address }}</dd>
                    </div>
                </dl>

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('transport.index') }}" class="inline-flex rounded-2xl bg-[rgb(var(--brand))] px-6 py-3 font-extrabold text-white">View pickup dates</a>
                    <a href="/" class="inline-flex rounded-2xl border border-slate-300 px-6 py-3 font-extrabold text-slate-800">Return home</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
