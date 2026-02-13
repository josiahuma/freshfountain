@extends('layouts.site')

@section('content')
@php
  use App\Models\Page;
  use Illuminate\Support\Facades\Storage;

  // Pull all course pages (template = course)
  $courses = Page::query()
    ->where('template', 'course')
    ->where('is_published', true)
    ->orderBy('title')
    ->get();

  $kicker = data_get($page->sections, 'courses_index.kicker', 'COURSES');
  $headline = data_get($page->sections, 'courses.headline', 'Grow in knowledge and faith');
  $title = data_get($page->sections, 'courses_index.title', $page->title ?? 'Courses');
  $subtitle = data_get($page->sections, 'courses_index.subtitle', $page->excerpt ?? 'Explore our courses and take your next step.');
  $body = data_get($page->sections, 'courses.body', 'Join a course, grow in knowledge, and deepen your faith.');
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

    @if($courses->count() === 0)
      <div class="rounded-3xl border border-slate-200 bg-slate-50 p-10 text-center">
        <div class="text-2xl font-extrabold text-slate-900">No courses yet</div>
        <p class="mt-2 text-slate-600">Create pages with template “Course Page” to show them here.</p>
      </div>
    @else
      <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($courses as $c)
          @php
            $imgUrl = $c->banner_image ? Storage::url($c->banner_image) : null;

             // ✅ FIX: read from sections.course.* (not sections.unit.*)
             $headline = data_get($c->sections, 'course.headline', $c->title);
            $btnText = data_get($c->sections, 'course.button_text', 'Take Course');
            $btnLink = data_get($c->sections, 'course.button_link', 'https://courses.freshfountain.org');
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
                  Upload a banner for this course
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
