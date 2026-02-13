@extends('layouts.site')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;
  use App\Models\Page;

  // Right-side image (page banner)
  $rightImage = $page->banner_image ? Storage::url($page->banner_image) : null;

  // Main hero content
  $kicker = data_get($page->sections, 'course.kicker', 'BE PART OF A TEAM');
  $headline = data_get($page->sections, 'course.headline', 'Serving & Building the Church');
  $body = data_get($page->sections, 'course.body',
    'Membership at Fresh Fountain is a pathway into community, growth, and service. Complete the membership course to learn our vision, values, and how you can get involved.'
  );

  $btnText = data_get($page->sections, 'course.button_text', 'Take Course');
  $btnLink = data_get($page->sections, 'course.button_link', 'https://courses.freshfountain.org');

  // Courses tiles (slug-based)
  $courses = data_get($page->sections, 'courses.tiles', [
    [
      'slug' => 'membership',
      'title' => 'Membership Course',
      'description' => 'Learn our vision, mission, core values, and how to get involved.',
      'link' => 'https://courses.freshfountain.org',
    ],
    [
      'slug' => 'baptism',
      'title' => 'Baptism Class',
      'description' => 'Understand the meaning and significance of baptism.',
      'link' => 'https://courses.freshfountain.org',
    ],
  ]);

  // Fetch referenced pages once (avoid querying inside the loop)
  $courseSlugs = collect($courses)->pluck('slug')->filter()->unique()->values()->all();

  $coursePagesBySlug = Page::query()
    ->whereIn('slug', $courseSlugs)
    ->get()
    ->keyBy('slug');
@endphp

{{-- =========================================================
  SPLIT HERO SECTION
========================================================= --}}
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
          <a
            href="{{ $btnLink }}"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center justify-center rounded-xl bg-white px-7 py-3 font-extrabold text-black
                   hover:bg-white/90 transition"
          >
            {{ $btnText }}
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
          alt="Course"
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

{{-- =========================================================
  COURSES TILES SECTION
========================================================= --}}
<section class="bg-slate-50 border-t border-slate-200">
  <div class="max-w-[1400px] mx-auto px-6 py-20">

    <div class="max-w-3xl mb-12">
      <p class="text-sm font-extrabold uppercase tracking-wide text-slate-500">
        Courses
      </p>
      <h2 class="mt-3 text-3xl md:text-4xl font-extrabold text-slate-900">
        Grow in faith and understanding
      </h2>
    </div>

    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
      @foreach($courses as $course)
      @php
        $slug = data_get($course, 'slug');
        $refPage = $slug ? ($coursePagesBySlug[$slug] ?? null) : null;

        // Use referenced page banner as tile image
        $bannerPath = $refPage?->banner_image;
        $imgUrl = $bannerPath ? Storage::url($bannerPath) : null;
      @endphp

      <article class="group rounded-3xl bg-white border border-slate-200
                      shadow-[0_14px_40px_rgba(15,23,42,0.08)]
                      hover:shadow-[0_22px_60px_rgba(15,23,42,0.12)]
                      transition overflow-hidden">

        {{-- Image --}}
        <div class="h-[220px] bg-slate-200 overflow-hidden">
          @if($imgUrl)
            <img
              src="{{ $imgUrl }}"
              alt="{{ data_get($course, 'title') }}"
              class="h-full w-full object-cover group-hover:scale-105 transition duration-500"
            >
          @else
            <div class="h-full w-full flex items-center justify-center text-slate-500 font-semibold">
              Add a banner image to the “{{ $slug }}” page
            </div>
          @endif
        </div>

        {{-- Content --}}
        <div class="p-7">
          <h3 class="text-xl font-extrabold text-slate-900">
            {{ data_get($course, 'title') }}
          </h3>

          <p class="mt-3 text-slate-600 leading-relaxed">
            {{ data_get($course, 'description') }}
          </p>

          <div class="mt-6">
            <a
              href="{{ data_get($course, 'link') }}"
              target="_blank"
              rel="noopener"
              class="inline-flex items-center justify-center w-full rounded-xl
                    bg-black px-6 py-3 font-extrabold text-white
                    hover:bg-black/90 transition"
            >
              View course →
            </a>
          </div>
        </div>
      </article>
    @endforeach
    </div>
  </div>
</section>
@endsection
