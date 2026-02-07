@extends('layouts.site')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;
  use App\Models\Page;

  $banner = $page->banner_image ? Storage::url($page->banner_image) : null;

  // All services are simply Pages with template = "service"
  $services = Page::query()
      ->where('template', 'service')
      ->where('is_published', true)
      ->orderBy('title')
      ->get();
@endphp

{{-- HERO --}}
<section class="relative overflow-hidden bg-slate-950 text-white">
  @if($banner)
    <img src="{{ $banner }}" class="absolute inset-0 w-full h-full object-cover opacity-35" alt="">
  @endif
  <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/20"></div>

  <div class="relative max-w-[1400px] mx-auto px-4 py-20 md:py-28">
    <p class="text-sm font-extrabold uppercase tracking-wide text-white/70">Our Services</p>

    <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight">
      {{ $page->title }}
    </h1>

    @if($page->excerpt)
      <p class="mt-5 text-white/80 text-lg max-w-2xl leading-relaxed">
        {{ $page->excerpt }}
      </p>
    @endif
  </div>
</section>

{{-- SERVICES GRID --}}
<section class="bg-white">
  <div class="max-w-[1400px] mx-auto px-4 py-16">

    @if($services->count())
      <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($services as $s)
          @php
            $img = $s->banner_image ? Storage::url($s->banner_image) : null;
            $url = url('/' . ltrim($s->slug, '/'));
            $desc = $s->excerpt ?: Str::limit(strip_tags($s->content ?? ''), 140);
          @endphp

          <a href="{{ $url }}"
             class="group rounded-[34px] overflow-hidden border border-slate-200 bg-white
                    shadow-[0_14px_40px_rgba(15,23,42,0.10)]
                    hover:shadow-[0_22px_70px_rgba(15,23,42,0.18)]
                    transition-all duration-300 hover:-translate-y-1">

            <div class="relative aspect-[16/10] bg-slate-100 overflow-hidden">
              @if($img)
                <img src="{{ $img }}" alt="{{ $s->title }}"
                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">
              @else
                <div class="absolute inset-0 bg-gradient-to-tr from-[rgb(var(--brand))]/25 via-slate-900/10 to-transparent"></div>
              @endif

              <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-transparent to-transparent"></div>

              <div class="absolute bottom-4 left-4 right-4">
                <div class="inline-flex rounded-full bg-white/15 border border-white/15 px-4 py-2 text-white font-extrabold">
                  {{ $s->title }}
                </div>
              </div>
            </div>

            <div class="p-7">
              <p class="text-slate-600 leading-relaxed">
                {{ $desc }}
              </p>

              <div class="mt-5 inline-flex items-center gap-2 font-extrabold text-[rgb(var(--brand))]">
                View service <span class="transition group-hover:translate-x-1">→</span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    @else
      <div class="rounded-3xl border border-slate-200 bg-slate-50 p-10 text-slate-700">
        No services have been published yet.
      </div>
    @endif

    @if($page->content)
      <div class="mt-16 prose prose-slate max-w-none">
        {!! $page->content !!}
      </div>
    @endif
  </div>
</section>
@endsection
