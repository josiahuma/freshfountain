@extends('layouts.site')

@section('content')
<section class="min-h-[70vh] bg-slate-50 py-16 sm:py-24">
    <div class="mx-auto max-w-3xl px-4">
        <div class="overflow-hidden rounded-3xl border border-emerald-200 bg-white shadow-xl shadow-slate-200/60">
            <div class="bg-emerald-600 px-6 py-10 text-center text-white sm:px-10">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-white/15">
                    <svg class="h-11 w-11" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12.5 9.25 17 19 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h1 class="mt-6 text-3xl font-extrabold sm:text-4xl">Thank you for your generosity</h1>
                <p class="mx-auto mt-3 max-w-xl text-base leading-7 text-emerald-50">Your payment was completed securely through Stripe.</p>
            </div>

            <div class="px-6 py-8 sm:px-10 sm:py-10">
                @if($donation)
                    <dl class="divide-y divide-slate-200 rounded-2xl border border-slate-200">
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <dt class="text-sm font-semibold text-slate-500">Donation amount</dt>
                            <dd class="text-lg font-extrabold text-slate-950">{{ $donation->formatted_amount }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <dt class="text-sm font-semibold text-slate-500">Fund</dt>
                            <dd class="text-right font-bold text-slate-950">{{ $donation->donationFund?->name ?? 'Donation' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <dt class="text-sm font-semibold text-slate-500">Reference</dt>
                            <dd class="font-mono text-sm font-bold text-slate-950">DON-{{ str_pad((string) $donation->id, 8, '0', STR_PAD_LEFT) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <dt class="text-sm font-semibold text-slate-500">Status</dt>
                            <dd>
                                @if($donation->status === \App\Models\Donation::STATUS_PAID)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-emerald-700">Paid</span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-amber-700">
                                        <span class="h-2 w-2 animate-pulse rounded-full bg-amber-500"></span>
                                        Processing
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                @else
                    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 text-sm leading-6 text-blue-800">Your payment has been submitted. Confirmation may take a few moments.</div>
                @endif

                @if($donation && $donation->status !== \App\Models\Donation::STATUS_PAID)
                    <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-center text-sm font-semibold text-amber-800">We are confirming your payment. This page will refresh automatically.</div>
                @endif

                <p class="mt-6 text-center text-sm leading-6 text-slate-600">A record of your donation has been securely saved. Thank you for partnering with Fresh Fountain Christian Network.</p>

                <div class="mt-8 grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('giving.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3.5 font-extrabold text-slate-800 transition hover:bg-slate-50">Give again</a>
                    <a href="/" class="inline-flex items-center justify-center rounded-2xl bg-blue-700 px-5 py-3.5 font-extrabold text-white transition hover:bg-blue-800">Return home</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@if($donation && $donation->status !== \App\Models\Donation::STATUS_PAID)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const key = 'giving-confirmation-refresh-{{ $donation->id }}';
                const count = Number(sessionStorage.getItem(key) || 0);
                if (count < 6) {
                    sessionStorage.setItem(key, String(count + 1));
                    setTimeout(function () { window.location.reload(); }, 2000);
                } else {
                    sessionStorage.removeItem(key);
                }
            });
        </script>
    @endpush
@endif