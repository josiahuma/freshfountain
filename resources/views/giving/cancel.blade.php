@extends('layouts.site')

@section('content')
<section class="min-h-[70vh] bg-slate-50 py-16 sm:py-24">
    <div class="mx-auto max-w-3xl px-4">
        <div class="overflow-hidden rounded-3xl border border-amber-200 bg-white shadow-xl shadow-slate-200/60">
            <div class="bg-amber-500 px-6 py-10 text-center text-white sm:px-10">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-white/15">
                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M7 7l10 10M17 7 7 17" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                    </svg>
                </div>

                <h1 class="mt-6 text-3xl font-extrabold sm:text-4xl">
                    Payment cancelled
                </h1>

                <p class="mx-auto mt-3 max-w-xl text-base leading-7 text-amber-50">
                    Your payment was not completed and no money has been taken.
                </p>
            </div>

            <div class="px-6 py-8 sm:px-10 sm:py-10">
                @if($donation)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="text-sm font-semibold text-slate-500">Cancelled donation</div>
                        <div class="mt-2 flex items-end justify-between gap-4">
                            <div>
                                <div class="text-lg font-extrabold text-slate-950">
                                    {{ $donation->donationFund?->name ?? 'Donation' }}
                                </div>
                                <div class="mt-1 text-sm text-slate-600">
                                    Reference DON-{{ str_pad((string) $donation->id, 8, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>

                            <div class="text-2xl font-extrabold text-slate-950">
                                {{ $donation->formatted_amount }}
                            </div>
                        </div>
                    </div>
                @endif

                <p class="mt-6 text-center text-sm leading-6 text-slate-600">
                    You can return to the giving form and try again whenever you are ready.
                </p>

                <div class="mt-8 grid gap-3 sm:grid-cols-2">
                    <a
                        href="/giving"
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3.5 font-extrabold text-slate-800 transition hover:bg-slate-50"
                    >
                        Other ways to give
                    </a>

                    <a
                        href="{{ route('giving.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-blue-700 px-5 py-3.5 font-extrabold text-white transition hover:bg-blue-800"
                    >
                        Try again
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
