@extends('layouts.site')

@section('content')
@php
    $banner = $page->banner_image ? \Illuminate\Support\Facades\Storage::url($page->banner_image) : null;
@endphp

{{-- Banner --}}
<div class="relative w-full">
    @if($banner)
        <div class="h-[280px] md:h-[360px] w-full overflow-hidden">
            <img src="{{ $banner }}" alt="{{ $page->title }}" class="h-full w-full object-cover">
        </div>
        <div class="absolute inset-0 bg-black/40"></div>
    @else
        <div class="h-[180px] md:h-[220px] w-full bg-slate-900"></div>
    @endif

    <div class="absolute inset-0 flex items-center">
        <div class="max-w-[1400px] mx-auto px-4 w-full">
            <div class="max-w-3xl text-white">
                <h1 class="text-3xl md:text-5xl font-extrabold leading-tight">
                    {{ $page->title }}
                </h1>

                @if($page->excerpt)
                    <p class="mt-4 text-white/90 text-base md:text-lg leading-relaxed">
                        {{ $page->excerpt }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Content --}}
<div class="max-w-[1400px] mx-auto px-4 py-12">
    <div class="grid gap-10 lg:grid-cols-12">
        {{-- Left title block like the screenshot --}}
        <aside class="lg:col-span-4">
            <div class="sticky top-24">
                <h2 class="text-3xl font-extrabold">{{ $page->title }}</h2>
                <div class="mt-4 h-1 w-20 bg-sky-500"></div>
            </div>
        </aside>

        {{-- Right content --}}
        <article class="lg:col-span-8">
            <div class="prose prose-slate max-w-none prose-headings:font-bold prose-h2:text-2xl prose-h3:text-xl">
                {!! $page->content !!}
            </div>
        </article>
    </div>
</div>
@endsection
