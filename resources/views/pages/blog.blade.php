@extends('layouts.site')

@section('content')
@php
  $banner = $page->banner_image ? asset('storage/' . $page->banner_image) : null;

  $posts = \App\Models\BlogPost::published()
      ->orderByDesc('published_at')
      ->orderByDesc('id')
      ->paginate(9);

  $featured = $posts->first(); // first item on page becomes "featured"
@endphp

{{-- HERO BANNER (PT Care editorial) --}}
<section class="relative overflow-hidden">
  @if($banner)
    <div class="h-[320px] md:h-[420px] w-full overflow-hidden">
      <img src="{{ $banner }}" class="h-full w-full object-cover" alt="">
    </div>
    <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/85 via-slate-900/55 to-emerald-900/25"></div>
  @else
    <div class="h-[260px] md:h-[340px] w-full bg-slate-950"></div>
    <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/90 via-slate-900/70 to-emerald-900/25"></div>
  @endif

  <div class="absolute inset-0">
    <div class="max-w-[1400px] mx-auto px-4 h-full flex items-end">
      <div class="pb-12 md:pb-16 max-w-3xl text-white">
        <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-white/90">
          <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
          Updates & Advice
        </div>

        <h1 class="mt-5 text-3xl md:text-6xl font-extrabold leading-tight">
          {{ $page->title }}
        </h1>

        @if($page->excerpt)
          <p class="mt-4 text-white/85 text-base md:text-lg leading-relaxed">
            {{ $page->excerpt }}
          </p>
        @endif
      </div>
    </div>
  </div>
</section>

{{-- CONTENT --}}
<section class="bg-slate-50">
  <div class="max-w-[1400px] mx-auto px-4 py-16">

    {{-- Top bar (search UI placeholder) --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <p class="text-sm font-extrabold uppercase tracking-wide text-emerald-700">Latest articles</p>
        <h2 class="mt-2 text-2xl md:text-3xl font-extrabold text-slate-900">
          Care news, guidance & community updates
        </h2>
      </div>

      {{-- UI-only search (optional later to wire to real search route) --}}
      <div class="w-full md:w-[420px]">
        <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
          <span class="px-3 text-slate-400">🔎</span>
          <input
            type="text"
            placeholder="Search articles (coming soon)…"
            class="w-full bg-transparent px-2 py-2 text-sm text-slate-700 placeholder:text-slate-400 outline-none"
            disabled
          >
          <span class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-extrabold text-white opacity-60">
            Search
          </span>
        </div>
        <p class="mt-2 text-xs text-slate-500">
          (Optional next step: we can wire real search + categories.)
        </p>
      </div>
    </div>

    {{-- FEATURED POST (big editorial layout) --}}
    @if($featured)
      @php
        $fImg = $featured->featured_image_url;
        $fTitle = $featured->title;
        $fDate = $featured->published_at ? $featured->published_at->format('M j, Y') : null;
        $fExcerpt = $featured->excerpt;
        $fLink = url('/blog/' . $featured->slug);
      @endphp

      <div class="mt-10">
        <a href="{{ $fLink }}"
           class="group grid gap-0 overflow-hidden rounded-3xl border border-slate-200 bg-white
                  shadow-[0_18px_60px_rgba(15,23,42,0.12)] transition hover:-translate-y-1
                  hover:shadow-[0_28px_90px_rgba(15,23,42,0.18)] lg:grid-cols-[1.25fr_1fr]">

          {{-- Image --}}
          <div class="relative min-h-[260px] bg-slate-100 overflow-hidden">
            @if($fImg)
              <img src="{{ $fImg }}" alt="{{ $fTitle }}"
                   class="absolute inset-0 h-full w-full object-cover group-hover:scale-105 transition duration-700">
            @endif
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/45 via-transparent to-transparent"></div>

            <div class="absolute top-6 left-6">
              <span class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-white">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                Featured
              </span>
            </div>
          </div>

          {{-- Copy --}}
          <div class="p-7 md:p-10 flex flex-col justify-center">
            @if($fDate)
              <div class="text-xs font-extrabold text-emerald-700 uppercase tracking-wide">
                {{ $fDate }}
              </div>
            @endif

            <h3 class="mt-3 text-2xl md:text-3xl font-extrabold text-slate-900 leading-tight">
              {{ $fTitle }}
            </h3>

            @if($fExcerpt)
              <p class="mt-4 text-slate-600 leading-relaxed">
                {{ \Illuminate\Support\Str::limit($fExcerpt, 190) }}
              </p>
            @endif

            <div class="mt-6 inline-flex items-center gap-2 text-emerald-700 font-extrabold">
              Read featured story <span class="transition group-hover:translate-x-1">→</span>
            </div>
          </div>
        </a>
      </div>
    @endif

    {{-- GRID: remaining posts (skip first item) --}}
    <div class="mt-10 grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
      @forelse($posts as $post)
        @php
          // skip the first item (already featured)
          if ($featured && $post->id === $featured->id) continue;

          $imgUrl = $post->featured_image_url;
          $title = $post->title;
          $date = $post->published_at ? $post->published_at->format('M j, Y') : null;
          $excerpt = $post->excerpt;
          $link = url('/blog/' . $post->slug);
        @endphp

        <a href="{{ $link }}"
           class="group overflow-hidden rounded-3xl border border-slate-200 bg-white
                  shadow-[0_14px_40px_rgba(15,23,42,0.10)] hover:shadow-[0_22px_70px_rgba(15,23,42,0.18)]
                  transition-all duration-300 hover:-translate-y-1">

          <div class="relative aspect-[16/11] bg-slate-100 overflow-hidden">
            @if($imgUrl)
              <img src="{{ $imgUrl }}" alt="{{ $title }}"
                   class="h-full w-full object-cover group-hover:scale-105 transition duration-700">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-transparent to-transparent"></div>
          </div>

          <div class="p-6">
            @if($date)
              <div class="text-xs font-extrabold text-emerald-700 uppercase tracking-wide">
                {{ $date }}
              </div>
            @endif

            <h2 class="mt-2 text-lg md:text-xl font-extrabold text-slate-900 leading-snug">
              {{ $title }}
            </h2>

            @if($excerpt)
              <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                {{ \Illuminate\Support\Str::limit($excerpt, 140) }}
              </p>
            @endif

            <div class="mt-5 inline-flex items-center gap-2 text-emerald-700 font-extrabold text-sm">
              Read more <span class="transition group-hover:translate-x-1">→</span>
            </div>
          </div>
        </a>
      @empty
        <div class="rounded-3xl border border-slate-200 bg-white p-10 text-slate-600">
          No blog posts yet.
        </div>
      @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-14">
      {{ $posts->links() }}
    </div>

  </div>
</section>
@endsection
