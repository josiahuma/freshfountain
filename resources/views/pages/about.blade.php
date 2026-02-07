@extends('layouts.site')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;

  $banner = $page->banner_image ? Storage::url($page->banner_image) : null;

  $about = $page->sections['about'] ?? [];
  if (is_string($about)) $about = json_decode($about, true) ?: [];

  $mission = data_get($about, 'mission');
  $vision  = data_get($about, 'vision');
  $verse   = data_get($about, 'verse');
  $values  = data_get($about, 'core_values', []);
@endphp

<style>
  .ff-soft { box-shadow: 0 18px 55px rgba(15,23,42,.10); }
</style>

{{-- =========================================================
  HERO
========================================================= --}}
<section class="relative overflow-hidden bg-slate-950 text-white">
  @if($banner)
    <img src="{{ $banner }}" class="absolute inset-0 w-full h-full object-cover opacity-45" alt="{{ $page->title }}">
  @endif
  <div class="absolute inset-0 bg-black/55"></div>
  <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/25"></div>

  <div class="relative max-w-[1400px] mx-auto px-4 py-20 md:py-28">
    <p class="text-sm font-extrabold uppercase tracking-widest text-white/70">About</p>

    <h1 class="mt-4 text-4xl md:text-6xl font-extrabold leading-tight">
      {{ $page->title }}
    </h1>

    @if($page->excerpt)
      <p class="mt-6 text-lg md:text-xl text-white/85 leading-relaxed max-w-3xl">
        {{ $page->excerpt }}
      </p>
    @endif
  </div>
</section>

{{-- =========================================================
  MISSION / VISION / VALUES (Matches screenshot layout)
========================================================= --}}
<section class="bg-white">
  <div class="max-w-[1400px] mx-auto px-4 py-16">

    <div class="grid gap-12 lg:grid-cols-12">

      {{-- LEFT: Mission + Vision --}}
      <div class="lg:col-span-7">

        {{-- Mission --}}
        <div>
          <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">Our Mission</h2>
          <div class="mt-3 h-1 w-14 bg-[rgb(var(--brand))]"></div>

          <p class="mt-6 text-slate-600 leading-relaxed text-lg max-w-2xl">
            {{ $mission }}
          </p>
        </div>

        {{-- spacing --}}
        <div class="h-14"></div>

        {{-- Vision --}}
        <div>
          <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">Our Vision</h2>
          <div class="mt-3 h-1 w-14 bg-[rgb(var(--brand))]"></div>

          <p class="mt-6 text-slate-600 leading-relaxed text-lg max-w-2xl">
            {{ $vision }}
          </p>
        </div>

      </div>

      {{-- RIGHT: Core Values --}}
      <div class="lg:col-span-5">
        <div class="rounded-[28px] border border-slate-200 bg-white p-8 ff-soft">

          <h2 class="text-3xl font-extrabold text-slate-900">Our Core Values</h2>
          <div class="mt-3 h-1 w-14 bg-[rgb(var(--brand))]"></div>

          @if($verse)
            <p class="mt-6 text-slate-600 leading-relaxed">
              {!! nl2br(e($verse)) !!}
            </p>
          @endif

          @if(is_array($values) && count($values))
            <ul class="mt-8 space-y-5">
              @foreach($values as $v)
                @php
                  $text = is_array($v) ? data_get($v, 'value') : $v;
                @endphp
                @if($text)
                  <li class="flex gap-4">
                    <span class="mt-2 h-1 w-4 shrink-0 rounded-full bg-[rgb(var(--brand))]"></span>
                    <span class="text-slate-800 leading-relaxed">
                      {{ $text }}
                    </span>
                  </li>
                @endif
              @endforeach
            </ul>
          @else
            <div class="mt-8 text-slate-600">
              No core values added yet.
            </div>
          @endif

        </div>
      </div>

    </div>

  </div>
</section>

@endsection
