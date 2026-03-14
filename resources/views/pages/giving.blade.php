@extends('layouts.site')

@section('content')
@php
    $sections = $page->sections ?? [];
    $giving = data_get($sections, 'giving', []);

    $banner = $page->banner_image
        ? asset('storage/' . $page->banner_image)
        : asset('images/default-banner.jpg');

    $methods = data_get($giving, 'methods', []);
@endphp

{{-- =========================================================
    PAGE HERO
========================================================= --}}
<section class="relative overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0">
        <img
            src="{{ $banner }}"
            alt="{{ $page->title }}"
            class="h-full w-full object-cover opacity-40"
        >
        <div class="absolute inset-0 bg-slate-950/65"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8 lg:py-32">
        @if(data_get($giving, 'kicker'))
            <p class="mb-4 text-sm font-semibold uppercase tracking-[0.25em] text-blue-300">
                {{ data_get($giving, 'kicker') }}
            </p>
        @endif

        <h1 class="max-w-4xl text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
            {{ $page->title }}
        </h1>

        @if($page->excerpt)
            <p class="mt-6 max-w-3xl text-lg leading-8 text-slate-200">
                {{ $page->excerpt }}
            </p>
        @endif
    </div>
</section>

{{-- =========================================================
    INTRO
========================================================= --}}
<section class="bg-white py-16 lg:py-24">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
        @if(data_get($giving, 'hero_title'))
            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                {{ data_get($giving, 'hero_title') }}
            </h2>
        @endif

        @if(data_get($giving, 'hero_text'))
            <p class="mt-6 text-lg leading-8 text-slate-600">
                {{ data_get($giving, 'hero_text') }}
            </p>
        @endif
    </div>
</section>

{{-- =========================================================
    ONLINE GIVING + BANK DETAILS
========================================================= --}}
<section class="bg-slate-50 py-16 lg:py-24">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">

        {{-- ONLINE GIVING CARD --}}
        <div class="rounded-3xl bg-white p-8 shadow-xl ring-1 ring-slate-200 lg:p-10">
            <div class="mb-6 inline-flex rounded-full bg-blue-100 px-4 py-1.5 text-sm font-semibold text-blue-700">
                Online Giving
            </div>

            <h3 class="text-3xl font-bold text-slate-900 sm:text-4xl">
                {{ data_get($giving, 'online_title', 'Give Online') }}
            </h3>

            @if(data_get($giving, 'online_text'))
                <p class="mt-5 max-w-xl text-lg leading-8 text-slate-600">
                    {{ data_get($giving, 'online_text') }}
                </p>
            @endif

            <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:items-center">
                @if(data_get($giving, 'online_button_link'))
                    <a href="{{ data_get($giving, 'online_button_link') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex min-h-[64px] items-center justify-center rounded-2xl bg-blue-600 px-10 py-5 text-lg font-bold text-white shadow-[0_18px_40px_rgba(37,99,235,0.28)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                        {{ data_get($giving, 'online_button_text', 'Give Now') }}
                    </a>
                @endif

                @if(data_get($giving, 'online_secondary_button_link'))
                    <a href="{{ data_get($giving, 'online_secondary_button_link') }}"
                       class="inline-flex min-h-[56px] items-center justify-center rounded-2xl border border-slate-300 px-8 py-4 text-base font-semibold text-slate-700 transition hover:bg-slate-100">
                        {{ data_get($giving, 'online_secondary_button_text', 'Contact Us') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- BANK DETAILS CARD --}}
        <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-xl lg:p-10">
            <div class="mb-6 inline-flex rounded-full bg-blue-500/15 px-4 py-1.5 text-sm font-semibold text-blue-300">
                Bank Transfer
            </div>

            <h3 class="text-2xl font-bold sm:text-3xl">
                {{ data_get($giving, 'bank_title', 'Bank Details') }}
            </h3>

            <div class="mt-8 space-y-5">
                <div class="border-b border-white/10 pb-4">
                    <p class="text-sm uppercase tracking-wide text-slate-400">Bank</p>
                    <p class="mt-1 text-lg font-semibold">{{ data_get($giving, 'bank_name') }}</p>
                </div>

                <div class="border-b border-white/10 pb-4">
                    <p class="text-sm uppercase tracking-wide text-slate-400">Account Name</p>
                    <p class="mt-1 text-lg font-semibold">{{ data_get($giving, 'account_name') }}</p>
                </div>

                @if(data_get($giving, 'sort_code'))
                    <div class="border-b border-white/10 pb-4">
                        <p class="text-sm uppercase tracking-wide text-slate-400">Sort Code / Branch Code</p>
                        <p class="mt-1 text-lg font-semibold">{{ data_get($giving, 'sort_code') }}</p>
                    </div>
                @endif

                <div class="border-b border-white/10 pb-4">
                    <p class="text-sm uppercase tracking-wide text-slate-400">Account Number</p>
                    <p class="mt-1 text-lg font-semibold">{{ data_get($giving, 'account_number') }}</p>
                </div>

                @if(data_get($giving, 'reference'))
                    <div class="border-b border-white/10 pb-4">
                        <p class="text-sm uppercase tracking-wide text-slate-400">Reference</p>
                        <p class="mt-1 text-lg font-semibold">{{ data_get($giving, 'reference') }}</p>
                    </div>
                @endif
            </div>

            @if(data_get($giving, 'bank_note'))
                <p class="mt-8 text-sm leading-7 text-slate-300">
                    {{ data_get($giving, 'bank_note') }}
                </p>
            @endif
        </div>
    </div>
</section>

{{-- =========================================================
    OTHER METHODS
========================================================= --}}
@if(is_array($methods) && count($methods))
<section class="bg-white py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                Other Ways To Give
            </h2>
        </div>

        <div class="mt-12 flex justify-center">
            <div class="w-full max-w-xl">
                @foreach($methods as $method)
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8 shadow-sm text-center sm:p-10">
                        <h3 class="text-2xl font-bold text-slate-900">
                            {{ $method['title'] ?? 'Giving Method' }}
                        </h3>

                        @if(!empty($method['text']))
                            <p class="mt-4 text-base leading-8 text-slate-600">
                                {{ $method['text'] }}
                            </p>
                        @endif

                        @if(!empty($method['button_link']))
                            <div class="mt-7 flex justify-center">
                                <a href="{{ $method['button_link'] }}"
                                   class="inline-flex min-h-[54px] items-center justify-center rounded-xl bg-blue-600 px-8 py-4 text-base font-semibold text-white shadow-lg transition hover:bg-blue-700">
                                    {{ $method['button_text'] ?? 'Learn More' }}
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- =========================================================
    EXTRA CONTENT
========================================================= --}}
@if($page->content)
<section class="bg-slate-50 py-16 lg:py-20">
    <div class="prose prose-lg mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        {!! $page->content !!}
    </div>
</section>
@endif
@endsection