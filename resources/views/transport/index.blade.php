@extends('layouts.site')

@section('title', 'Church Transport | Fresh Fountain')

@section(
    'meta_description',
    'Need a ride to church? Book your Fresh Fountain transport pickup for upcoming services and events.'
)

@section('og_title', 'Church Transport | Fresh Fountain')

@section(
    'og_description',
    'Need a ride to church? Book your Fresh Fountain transport pickup for upcoming services and events.'
)

@section(
    'og_url',
    'https://freshfountain.org/transport'
)

@section(
    'og_image',
    'https://freshfountain.org/images/transport-share.jpg?v=2'
)

@section('content')
@php($title = 'Church Transport | Fresh Fountain')

<section class="bg-slate-950 text-white">
    <div class="mx-auto max-w-[1200px] px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <p class="text-sm font-extrabold uppercase tracking-[0.18em] text-blue-300">Fresh Fountain Transport</p>
        <h1 class="mt-3 max-w-3xl text-4xl font-extrabold tracking-tight md:text-6xl">Book your church pickup</h1>
        <p class="mt-5 max-w-2xl text-lg leading-8 text-white/75">
            Choose an available service date and pickup time. Your booking goes directly to the Fresh Fountain transport team.
        </p>
    </div>
</section>

<section class="bg-slate-50">
    <div class="mx-auto max-w-[1200px] px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        @if($events->isEmpty())
            <div class="rounded-3xl border border-slate-200 bg-white p-8 md:p-12">
                <h2 class="text-2xl font-extrabold text-slate-950">No pickup bookings are open right now</h2>
                <p class="mt-3 text-slate-600">Please check again later or contact the church if you need transport assistance.</p>
                <a href="/contact" class="mt-6 inline-flex rounded-2xl bg-[rgb(var(--brand))] px-6 py-3 font-extrabold text-white">Contact us</a>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2">
                @foreach($events as $event)
                    @php($slots = $event->availableSlots())
                    @php($availableCount = $slots->where('full', false)->count())

                    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_16px_45px_rgba(15,23,42,0.08)]">
                        <div class="border-b border-slate-200 bg-slate-950 px-6 py-5 text-white">
                            <div class="text-sm font-extrabold uppercase tracking-[0.14em] text-blue-300">
                                {{ $event->pickup_date->format('l, j F Y') }}
                            </div>
                            <h2 class="mt-2 text-2xl font-extrabold">{{ $event->title }}</h2>
                        </div>

                        <div class="p-6">
                            <dl class="grid gap-4 sm:grid-cols-2">
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Pickup window</dt>
                                    <dd class="mt-1 font-extrabold text-slate-950">
                                        {{ \Carbon\Carbon::parse($event->pickup_start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($event->pickup_end_time)->format('g:i A') }}
                                    </dd>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Available times</dt>
                                    <dd class="mt-1 font-extrabold text-slate-950">{{ $availableCount }} slot{{ $availableCount === 1 ? '' : 's' }}</dd>
                                </div>
                            </dl>

                            <a href="{{ route('transport.book', $event) }}"
                               class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-[rgb(var(--brand))] px-6 py-3.5 font-extrabold text-white transition hover:bg-[rgb(var(--brand-dark))]">
                                Choose pickup time →
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
