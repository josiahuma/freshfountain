@extends('layouts.site')

@section('content')
@php
  use App\Models\Page;
  use Illuminate\Support\Facades\Storage;

  // ✅ Pull all unit pages (template = units)
  $units = Page::query()
    ->where('template', 'units')
    ->where('is_published', true)
    ->orderBy('title')
    ->get();

  $kicker   = data_get($page->sections, 'units_index.kicker', 'UNITS & TEAMS');
  $headline = data_get($page->sections, 'units.headline', 'Serving & Building the Church');
  $title    = data_get($page->sections, 'units_index.title', $page->title ?? 'Units');
  $subtitle = data_get($page->sections, 'units_index.subtitle', $page->excerpt ?? 'Explore our units and take your next step.');
  $body     = data_get($page->sections, 'units.body', 'Join a team, grow in community, and serve with purpose.');
@endphp

{{-- HERO --}}
<section class="relative overflow-hidden bg-[rgb(var(--dark))] text-white">
  <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/25"></div>

  <div class="relative max-w-[1400px] mx-auto px-4 py-20 md:py-28">
    <div class="max-w-3xl">
      <p class="text-sm font-extrabold uppercase tracking-wide text-white/70">
        {{ $kicker }}
      </p>

      <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight">
        {{ $title }}
      </h1>

      @if($subtitle)
        <p class="mt-6 text-lg md:text-xl text-white/80 leading-relaxed max-w-2xl">
          {{ $subtitle }}
        </p>
      @endif
    </div>
  </div>
</section>

{{-- LIST --}}
<section class="bg-white">
  <div class="max-w-[1400px] mx-auto px-4 py-16 md:py-20">

    @if($units->count() === 0)
      <div class="rounded-3xl border border-slate-200 bg-slate-50 p-10 text-center">
        <div class="text-2xl font-extrabold text-slate-900">No units yet</div>
        <p class="mt-2 text-slate-600">Create pages with template “Unit Page” (template key: <b>units</b>) to show them here.</p>
      </div>
    @else
      <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($units as $c)
          @php
            $imgUrl  = $c->banner_image ? Storage::url($c->banner_image) : null;

            // ✅ FIX: read from sections.units.* (not sections.unit.*)
            $headline = data_get($c->sections, 'units.headline', $c->title);
            $btnText  = data_get($c->sections, 'units.button_text', 'View unit');
            $btnLink  = data_get($c->sections, 'units.button_link', '/'.$c->slug);
          @endphp

          <article class="group rounded-3xl bg-white border border-slate-200
                          shadow-[0_14px_40px_rgba(15,23,42,0.08)]
                          hover:shadow-[0_22px_60px_rgba(15,23,42,0.12)]
                          transition overflow-hidden">

            {{-- Image --}}
            <a href="/{{ $c->slug }}" class="block">
            <div class="h-[220px] bg-slate-200 overflow-hidden">
              @if($imgUrl)
                <img
                  src="{{ $imgUrl }}"
                  alt="{{ $c->title }}"
                  class="h-full w-full object-cover group-hover:scale-105 transition duration-500"
                >
              @else
                <div class="h-full w-full flex items-center justify-center text-slate-500 font-semibold">
                  Upload a banner for this unit
                </div>
              @endif
            </div>

            {{-- Content --}}
            <div class="p-7">
              <h3 class="text-xl font-extrabold text-slate-900">
                {!! nl2br(e($headline)) !!}
              </h3>

              @if($c->excerpt)
                <p class="mt-3 text-slate-600 leading-relaxed">
                  {{ $c->excerpt }}
                </p>
              @endif
            </div>
          </a>
          </article>
        @endforeach
      </div>
    @endif

  </div>
</section>
@endsection
