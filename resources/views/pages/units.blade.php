@extends('layouts.site')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;
  use App\Models\Page;

  $rightImage = $page->banner_image ? Storage::url($page->banner_image) : null;

  // ✅ Reads from sections.units.* (matches your Filament)
  $kicker   = data_get($page->sections, 'units.kicker', 'BE PART OF A TEAM');
  $headline = data_get($page->sections, 'units.headline', 'Serving & Building the Church');
  $body     = data_get($page->sections, 'units.body', 'Join a team, grow in community, and serve with purpose.');
  $btnText  = data_get($page->sections, 'units.button_text', 'Join Unit');
  $btnLink  = data_get($page->sections, 'units.button_link', '/contact');

  // Tiles (slug-based)
  $tiles = data_get($page->sections, 'units.tiles', [
    [
      'slug' => 'media-unit',
      'title' => 'Media Unit',
      'description' => 'Get involved with Media Unit and help us create engaging content to share our message.',
      'link' => 'https://courses.freshfountain.org',
    ],
  ]);

  $tilesSlugs = collect($tiles)->pluck('slug')->filter()->unique()->values()->all();

  $pagesBySlug = Page::query()
    ->whereIn('slug', $tilesSlugs)
    ->where('is_published', true)
    ->get()
    ->keyBy('slug');
@endphp

{{-- SPLIT HERO --}}
<section class="bg-white">
  <div class="grid lg:grid-cols-2 min-h-[520px]">

    {{-- LEFT --}}
    <div class="bg-black text-white flex items-center">
      <div class="w-full px-6 sm:px-10 py-14 md:py-20 max-w-[700px] mx-auto">
        <div class="text-sm font-extrabold tracking-widest text-white/70 uppercase">
          {{ $kicker }}
        </div>

        <h1 class="mt-6 text-4xl sm:text-5xl md:text-6xl leading-[1.03] font-extrabold italic tracking-tight">
          {!! nl2br(e($headline)) !!}
        </h1>

        <p class="mt-6 text-white/75 text-base sm:text-lg leading-relaxed max-w-xl">
          {{ $body }}
        </p>

        <div class="mt-10">
          <a href="{{ $btnLink }}"
             @if(str_starts_with($btnLink, 'http')) target="_blank" rel="noopener" @endif
             class="inline-flex items-center justify-center rounded-xl bg-white px-7 py-3 font-extrabold text-black
                    hover:bg-white/90 transition">
            {{ $btnText }}
          </a>
          <a href="/units" class="ml-6 inline-flex items-center justify-center rounded-xl bg-white/20 px-7 py-3 font-extrabold text-white
                    hover:bg-white/30 transition">
            View Units
          </a>
        </div>
      </div>
    </div>

    {{-- RIGHT --}}
    <div class="relative overflow-hidden bg-slate-200
                aspect-[4/3] sm:aspect-[16/10] md:aspect-auto md:h-full">
      @if($rightImage)
        <img
          src="{{ $rightImage }}"
          alt="{{ $page->title }}"
          class="absolute inset-0 h-full w-full object-contain md:object-cover"
        >
        <div class="absolute inset-0 bg-black/10"></div>
      @else
        <div class="absolute inset-0 flex items-center justify-center text-slate-600 font-semibold">
          Upload a banner image on this page to show it here.
        </div>
      @endif
    </div>

  </div>
</section>
@endsection
