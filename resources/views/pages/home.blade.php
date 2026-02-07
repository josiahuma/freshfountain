@extends('layouts.site')

@section('content')
@php
  use App\Models\BlogPost;
  use Illuminate\Support\Str;

  // =========================
  // CMS Sections
  // =========================
  $hero     = data_get($page->sections, 'hero', []);
  $about    = data_get($page->sections, 'about', []);
  $services = data_get($page->sections, 'services.items', []);   // treat as "What's On / Events"
  $servicesTitle = data_get($page->sections, 'services.title', "What's On");

  $ministries = data_get($page->sections, 'cards.items', []);    // treat as "Ministries"
  $cta       = data_get($page->sections, 'practices', []);       // treat as "Come Worship / CTA band"

  $blogSection    = data_get($page->sections, 'blog_section', []);
  $blogTitle      = data_get($blogSection, 'title', 'Latest News');
  $blogButtonText = data_get($blogSection, 'button_text', 'View all posts →');
  $blogButtonLink = data_get($blogSection, 'button_link', '/blog');
  $blogLimit      = (int) data_get($blogSection, 'limit', 3);

  $latestPosts = BlogPost::published()
      ->orderByDesc('published_at')
      ->limit($blogLimit)
      ->get();

  $logos = data_get($page->sections, 'logos.items', []);

  // =========================
  // HERO MEDIA
  // =========================
  $type = data_get($hero, 'type', 'video'); // video | slider | image

  $bg = data_get($hero, 'background_image');
  $bgUrl = $bg ? asset('storage/' . $bg) : null;

  $videoFile = data_get($hero, 'video_file');
  $videoFileUrl = $videoFile ? asset('storage/' . $videoFile) : null;

  $slides = data_get($hero, 'slides', []);

  // Embed URL fallback (YouTube/Vimeo)
  $videoUrl = data_get($hero, 'video_url');
  $embedUrl = null;
  if ($videoUrl) {
      if (preg_match('~vimeo\.com/(\d+)~', $videoUrl, $m)) {
          $embedUrl = "https://player.vimeo.com/video/{$m[1]}?background=1&autoplay=1&loop=1&muted=1&controls=0";
      }
      if (! $embedUrl) {
          if (preg_match('~youtu\.be/([^\?&]+)~', $videoUrl, $m)) {
              $id = $m[1];
              $embedUrl = "https://www.youtube.com/embed/{$id}?autoplay=1&mute=1&loop=1&playlist={$id}&controls=0&showinfo=0&modestbranding=1";
          } elseif (preg_match('~watch\?v=([^\?&]+)~', $videoUrl, $m)) {
              $id = $m[1];
              $embedUrl = "https://www.youtube.com/embed/{$id}?autoplay=1&mute=1&loop=1&playlist={$id}&controls=0&showinfo=0&modestbranding=1";
          }
      }
  }

  // if slider, pick current slide image (we won't use swiper here for a calmer church feel)
  $heroSlideImgUrl = null;
  if ($type === 'slider' && is_array($slides) && count($slides)) {
      $first = $slides[0] ?? [];
      $img = data_get($first, 'image');
      $heroSlideImgUrl = $img ? asset('storage/' . $img) : null;
  }

  // =========================
  // SERVICE TIMES (church feel)
  // You can later wire these to CMS fields if you want
  // =========================
  $times = data_get($page->sections, 'service_times.items', []);
  if (!is_array($times) || !count($times)) {
      $times = [
        ['title' => 'Sunday Encounter', 'text' => '10:30 AM'],
        ['title' => 'Tuesday Overflow (Online Bible Study)', 'text' => 'Tuesday • 7:00 PM'],
        ['title' => 'Refresh (Worship & Miracle Service)', 'text' => '1st Fridays • 7:00 PM'],
      ];
  }

  // Ministries title/subtitle (from cards.* if present)
  $minTitle = data_get($page->sections, 'cards.title', 'Ministries');
  $minSub   = data_get($page->sections, 'cards.subtitle', 'Find your place. Grow and serve with others.');
@endphp

<style>
  /* Softer “church” typography + glow without changing tailwind config */
  .ff-hero-title { letter-spacing: -0.02em; }
  .ff-glow { box-shadow: 0 30px 90px rgba(0,0,0,.35); }
  .ff-soft { box-shadow: 0 18px 55px rgba(15,23,42,.10); }
  .ff-vignette { background: radial-gradient(ellipse at center, rgba(255,255,255,.10), rgba(0,0,0,.60)); }
</style>

{{-- =========================================================
  HERO — CHURCH STYLE (Heart Church inspired)
========================================================= --}}
@php
  $hero = data_get($page->sections, 'hero', []);
  $bg = data_get($hero, 'background_image');
  $bgUrl = $bg ? asset('storage/' . $bg) : null;

  $videoFile = data_get($hero, 'video_file');
  $videoFileUrl = $videoFile ? asset('storage/' . $videoFile) : null;
@endphp

<section class="relative">
  <div class="relative h-[85vh] min-h-[640px] overflow-hidden bg-black">

    {{-- Background --}}
    @if($videoFileUrl)
      <video
        class="absolute inset-0 w-full h-full object-cover"
        autoplay muted loop playsinline>
        <source src="{{ $videoFileUrl }}" type="video/mp4">
      </video>
    @elseif($bgUrl)
      <img
        src="{{ $bgUrl }}"
        class="absolute inset-0 w-full h-full object-cover"
        alt="">
    @endif

    {{-- Soft colour wash (VERY important for church look) --}}
    <div class="absolute inset-0 bg-gradient-to-br
                from-sky-200/60
                via-white/30
                to-rose-200/60">
    </div>

    {{-- Darken edges for readability --}}
    <div class="absolute inset-0 bg-black/35"></div>

    {{-- Content --}}
    <div class="relative h-full flex items-center justify-center text-center">
      <div class="max-w-[900px] px-4 text-white">

        <h1
          class="text-4xl md:text-6xl lg:text-7xl
                 font-serif font-semibold tracking-wide
                 leading-tight">
          {{ data_get($hero, 'title', 'Welcome to Fresh Fountain Church') }}
        </h1>

        @if(data_get($hero, 'subtitle'))
          <p class="mt-6 text-lg md:text-2xl text-white/85 max-w-2xl mx-auto">
            {{ data_get($hero, 'subtitle', 'A place to worship, grow, and belong') }}
          </p>
        @endif

        <div class="mt-10">
          <a
            href="{{ data_get($hero, 'primary_button_link', '/about') }}"
            class="inline-flex items-center justify-center
                   rounded-lg bg-[rgb(var(--brand))] px-8 py-4
                   text-white text-lg font-semibold
                   hover:bg-black/80 transition">
            {{ data_get($hero, 'primary_button_text', 'Our Heart') }}
          </a>
        </div>

      </div>
    </div>

  </div>
</section>


{{-- =========================================================
  SERVICE TIMES — strong church section, not corporate
========================================================= --}}
<section id="visit" class="bg-white">
  <div class="max-w-[1400px] mx-auto px-4 -mt-12 relative z-10">
    <div class="rounded-[36px] border border-slate-200 bg-white ff-soft p-7 md:p-10">
      <div class="grid gap-8 lg:grid-cols-[1.2fr_1fr] lg:items-center">
        <div>
          <p class="text-sm font-extrabold uppercase tracking-wide text-[rgb(var(--brand))]">Service times</p>
          <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-slate-900">
            {{ data_get($page->sections, 'service_times.title', 'Join our services') }}
          </h2>
          <p class="mt-3 text-slate-600 leading-relaxed max-w-2xl">
            {{ data_get($page->sections, 'service_times.subtitle', 'We’d love to welcome you in person. Come early, grab a seat, and feel at home.') }}
          </p>

          <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ data_get($page->sections, 'service_times.primary_link', '/contact#map') }}"
               class="inline-flex items-center justify-center rounded-2xl bg-[rgb(var(--brand))] px-7 py-3 text-white font-extrabold hover:bg-[rgb(var(--brand-dark))] transition">
              {{ data_get($page->sections, 'service_times.primary_text', 'Get Directions') }}
            </a>

            <a href="{{ data_get($page->sections, 'service_times.secondary_link', '/contact') }}"
               class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-7 py-3 text-slate-900 font-extrabold hover:bg-slate-100 transition">
              {{ data_get($page->sections, 'service_times.secondary_text', 'Contact Us') }}
            </a>
          </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
          @foreach($times as $t)
            <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5">
              <div class="text-sm font-extrabold text-slate-900">{{ data_get($t, 'title', 'Service') }}</div>
              <div class="mt-2 text-[rgb(var(--brand))] font-extrabold text-lg">
                {{ data_get($t, 'text', '') }}
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

{{-- =========================================================
  WELCOME / ABOUT — warmer and more “church homepage”
========================================================= --}}
<section class="bg-white">
  <div id="about-section" class="max-w-[1400px] mx-auto px-4 py-16">
    <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
      @php
        $aboutImg = data_get($about, 'image');
        $aboutImgUrl = $aboutImg ? asset('storage/' . $aboutImg) : null;
      @endphp

      <div class="order-2 lg:order-1">
        <p class="text-sm font-extrabold uppercase tracking-wide text-[rgb(var(--brand))]">
          {{ data_get($about, 'kicker', 'Welcome') }}
        </p>

        <h2 class="mt-2 text-3xl md:text-5xl font-extrabold text-slate-900">
          {{ data_get($about, 'title', 'We are a family of faith') }}
        </h2>

        <div class="mt-5 text-slate-600 leading-relaxed space-y-4 max-w-2xl">
          {!! data_get($about, 'body', '<p>We are a community centred on Jesus, worship, and the Word. Come as you are — there’s a place for you here.</p>') !!}
        </div>

        <div class="mt-8 flex gap-3 flex-wrap">
          <a href="{{ data_get($about, 'button_link', '/#ministries') }}"
             class="inline-flex items-center justify-center rounded-2xl bg-[rgb(var(--brand))] px-7 py-3 text-white font-extrabold hover:bg-[rgb(var(--brand-dark))] transition">
            {{ data_get($about, 'button_text', 'Explore Ministries') }}
          </a>
        </div>

        {{-- small “values” chips --}}
        <div class="mt-8 flex flex-wrap gap-2">
          @php
            $values = data_get($page->sections, 'values.items', []);
            if (!is_array($values) || !count($values)) {
              $values = ['Worship', 'The Word', 'Prayer', 'Community', 'Outreach'];
            }
          @endphp
          @foreach($values as $v)
            <span class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-extrabold text-slate-800">
              {{ is_array($v) ? data_get($v,'value') : $v }}
            </span>
          @endforeach
        </div>
      </div>

      <div class="order-1 lg:order-2 lg:justify-self-end">
        <div class="relative rounded-[36px] overflow-hidden border border-slate-200 bg-slate-100 ff-soft">
          @if($aboutImgUrl)
            <img src="{{ $aboutImgUrl }}" class="w-full max-w-[560px] aspect-[4/5] object-cover" alt="">
          @else
            <div class="w-full max-w-[560px] aspect-[4/5] bg-slate-100"></div>
          @endif
          <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-transparent to-transparent"></div>

          <div class="absolute bottom-5 left-5 right-5 rounded-3xl bg-white/10 border border-white/10 backdrop-blur p-5">
            <p class="text-white/90 font-semibold">
              “{{ data_get($about, 'quote', 'We love God. We love people. We live the Word.') }}”
            </p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- =========================================================
  WATCH ONLINE — YouTube LIVE (or latest replay fallback)
  - Detect LIVE vs REPLAY + red LIVE badge
  - Autoplay only when live
  - Title + date under video
  - Robust: /live scrape + RSS fallback (no API key needed)
========================================================= --}}
@php
  // ✅ CONFIG
  $ytChannelId  = 'UCihtEEZ1z9a7PvPTgZt5R5w';
  $ytHandle     = '@freshfountain';
  $ytStreamsUrl = "https://www.youtube.com/{$ytHandle}/streams";

  $extractVideoId = function (?string $url) {
      if (!$url) return null;
      if (preg_match('~v=([a-zA-Z0-9_-]{6,})~', $url, $m)) return $m[1];
      return null;
  };

  /**
   * STEP 1: Try to detect LIVE (and get videoId) via /live page.
   * If YouTube blocks server-side fetch, this may fail — that's why we have RSS fallback.
   */
  $liveInfo = \Illuminate\Support\Facades\Cache::remember('ff_youtube_live_info_v2', now()->addMinutes(2), function () use ($ytChannelId, $extractVideoId) {
      try {
          $res = \Illuminate\Support\Facades\Http::timeout(10)
              ->withHeaders([
                  'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122 Safari/537.36',
                  'Accept-Language' => 'en-GB,en;q=0.9',
              ])
              ->get("https://www.youtube.com/channel/{$ytChannelId}/live");

          if (!$res->ok()) {
              return ['is_live' => false, 'video_id' => null, 'source' => 'live_failed'];
          }

          $html = $res->body();

          // Extract og:url (contains watch?v=VIDEOID)
          $ogUrl = null;
          if (preg_match('~property="og:url"\s+content="([^"]+)"~', $html, $m)) {
              $ogUrl = html_entity_decode($m[1], ENT_QUOTES);
          }

          $videoId = $extractVideoId($ogUrl);

          // Live detection heuristics
          $isLive = false;
          if (preg_match('~"isLiveNow"\s*:\s*true~', $html)) $isLive = true;
          if (preg_match('~"badgeText"\s*:\s*"LIVE"~', $html)) $isLive = true;

          // Fallback: search any watch?v=... in HTML
          if (!$videoId && preg_match('~watch\?v=([a-zA-Z0-9_-]{6,})~', $html, $m2)) {
              $videoId = $m2[1];
          }

          return ['is_live' => (bool)$isLive, 'video_id' => $videoId, 'source' => 'live'];
      } catch (\Throwable $e) {
          return ['is_live' => false, 'video_id' => null, 'source' => 'live_exception'];
      }
  });

  /**
   * STEP 2: RSS fallback for latest replay (reliable, no API key).
   * Gives videoId + title + published date.
   */
  $rssInfo = \Illuminate\Support\Facades\Cache::remember('ff_youtube_rss_latest_v2', now()->addMinutes(15), function () use ($ytChannelId) {
      try {
          $rssUrl = "https://www.youtube.com/feeds/videos.xml?channel_id={$ytChannelId}";
          $res = \Illuminate\Support\Facades\Http::timeout(10)->get($rssUrl);
          if (!$res->ok()) return ['video_id' => null, 'title' => null, 'published' => null];

          $xml = @simplexml_load_string($res->body());
          if (!$xml) return ['video_id' => null, 'title' => null, 'published' => null];

          // YouTube namespaces
          $ns = $xml->getNamespaces(true);

          $entry = $xml->entry[0] ?? null;
          if (!$entry) return ['video_id' => null, 'title' => null, 'published' => null];

          $yt = $entry->children($ns['yt'] ?? null);
          $videoId = (string) ($yt->videoId ?? '');

          $title = (string) ($entry->title ?? '');
          $published = (string) ($entry->published ?? '');

          return [
              'video_id' => $videoId ?: null,
              'title' => $title ?: null,
              'published' => $published ?: null,
          ];
      } catch (\Throwable $e) {
          return ['video_id' => null, 'title' => null, 'published' => null];
      }
  });

  // Choose which video to embed:
  // - If LIVE has a videoId => use it
  // - Else use RSS latest replay
  $isLive = (bool) ($liveInfo['is_live'] ?? false);
  $videoId = $liveInfo['video_id'] ?? null;

  if (!$videoId) {
      $videoId = $rssInfo['video_id'] ?? null;
      $isLive = false; // RSS is replay/latest upload
  }

  // Title/date:
  // - Prefer watch-page meta if we can fetch
  // - Else fallback to RSS title/published
  $meta = \Illuminate\Support\Facades\Cache::remember('ff_youtube_meta_v2_' . ($videoId ?? 'none'), now()->addMinutes(30), function () use ($videoId) {
      if (!$videoId) return ['title' => null, 'date' => null];

      try {
          $res = \Illuminate\Support\Facades\Http::timeout(10)
              ->withHeaders([
                  'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122 Safari/537.36',
                  'Accept-Language' => 'en-GB,en;q=0.9',
              ])
              ->get("https://www.youtube.com/watch?v={$videoId}");

          if (!$res->ok()) return ['title' => null, 'date' => null];

          $html = $res->body();

          $title = null;
          if (preg_match('~property="og:title"\s+content="([^"]+)"~', $html, $m)) {
              $title = html_entity_decode($m[1], ENT_QUOTES);
          }

          $date = null;
          if (preg_match('~itemprop="datePublished"\s+content="([^"]+)"~', $html, $m2)) {
              $date = $m2[1];
          }

          return ['title' => $title, 'date' => $date];
      } catch (\Throwable $e) {
          return ['title' => null, 'date' => null];
      }
  });

  $title = $meta['title'] ?: ($rssInfo['title'] ?? null);

  // date: use meta datePublished OR RSS published
  $dateRaw = $meta['date'] ?: ($rssInfo['published'] ?? null);
  $prettyDate = null;

  if ($dateRaw) {
      try {
          $prettyDate = \Carbon\Carbon::parse($dateRaw)->format('jS F, Y');
      } catch (\Throwable $e) {
          $prettyDate = $dateRaw;
      }
  }

  // Autoplay only when live (muted to satisfy browser policies)
  $autoplay = $isLive ? '1' : '0';
  $mute     = $isLive ? '1' : '0';

  $embedSrc = $videoId
      ? "https://www.youtube.com/embed/{$videoId}?autoplay={$autoplay}&mute={$mute}&rel=0&modestbranding=1"
      : null;
@endphp

<section id="watch" class="bg-slate-50">
  <div class="max-w-[1400px] mx-auto px-4 py-16">
    <div class="rounded-[40px] overflow-hidden border border-slate-200 bg-white ff-soft">
      <div class="grid gap-0 lg:grid-cols-[1fr_1.7fr]">

        {{-- LEFT --}}
        <div class="p-10 md:p-14">
          <p class="text-sm font-extrabold uppercase tracking-wide text-[rgb(var(--brand))]">Watch online</p>

          <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-slate-900">
            {{ data_get($page->sections, 'watch.title', 'Worship from anywhere') }}
          </h2>

          <p class="mt-4 text-slate-600 leading-relaxed max-w-xl">
            {{ data_get($page->sections, 'watch.subtitle', 'Join our livestream, catch up on messages, and stay connected throughout the week.') }}
          </p>

          <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ $ytStreamsUrl }}" target="_blank" rel="noopener"
              class="inline-flex items-center justify-center rounded-2xl bg-[rgb(var(--brand))] px-7 py-3 text-white font-extrabold hover:bg-[rgb(var(--brand-dark))] transition">
              {{ $isLive ? 'Watch live' : 'Watch latest' }}
            </a>

            <a href="/blog"
              class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-7 py-3 text-slate-900 font-extrabold hover:bg-slate-100 transition">
              Messages & News
            </a>

            <a href="https://open.spotify.com/show/3hM7OjaL5YfXTBMLvo7chp?si=1e54ca0dd9544696"
              target="_blank" rel="noopener"
              class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-emerald-500 px-7 py-3 text-slate-900 font-extrabold hover:bg-emerald-600 text-white transition">
              🎧 Listen on Spotify
            </a>
          </div>


          <div class="mt-10 grid gap-3 sm:grid-cols-2">
            <div class="rounded-3xl bg-slate-50 border border-slate-200 p-5">
              <div class="text-sm font-extrabold text-slate-900">New here?</div>
              <div class="mt-2 text-slate-600">We’ll help you plan your first visit.</div>
              <a href="/#visit" class="mt-4 inline-flex font-extrabold text-[rgb(var(--brand))] hover:text-[rgb(var(--brand-dark))] transition">
                Plan a visit →
              </a>
            </div>

            <div class="rounded-3xl bg-slate-50 border border-slate-200 p-5">
              <div class="text-sm font-extrabold text-slate-900">Need prayer?</div>
              <div class="mt-2 text-slate-600">Send a request and we’ll stand with you.</div>
              <a href="/contact" class="mt-4 inline-flex font-extrabold text-[rgb(var(--brand))] hover:text-[rgb(var(--brand-dark))] transition">
                Request prayer →
              </a>
            </div>
          </div>
        </div>

        {{-- RIGHT (16:9 embed + details below, never covers play button) --}}
        <div class="bg-slate-900 p-8 md:p-10 flex flex-col justify-center">
          <div class="relative w-full max-w-[1100px] mx-auto aspect-video rounded-[28px] overflow-hidden bg-black border border-white/10 shadow-[0_22px_70px_rgba(0,0,0,0.35)]">
            @if($embedSrc)
              <iframe
                class="absolute inset-0 w-full h-full"
                src="{{ $embedSrc }}"
                title="Fresh Fountain Service"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen>
              </iframe>
            @else
              <div class="absolute inset-0 flex items-center justify-center text-white/80">
                Unable to load video right now.
              </div>
            @endif

            <div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-black/35 via-transparent to-black/25"></div>

            <div class="absolute top-5 left-5 z-10 pointer-events-none">
              @if($isLive)
                <div class="inline-flex items-center gap-2 rounded-full bg-red-600 text-white px-4 py-2 text-xs font-extrabold shadow-lg">
                  <span class="h-2 w-2 rounded-full bg-white"></span>
                  LIVE
                </div>
              @else
                <div class="inline-flex items-center gap-2 rounded-full bg-white/15 text-white px-4 py-2 text-xs font-extrabold border border-white/10 backdrop-blur">
                  LATEST REPLAY
                </div>
              @endif
            </div>
          </div>

          <div class="w-full max-w-[1100px] mx-auto mt-6">
            <div class="rounded-[28px] bg-white/10 border border-white/10 backdrop-blur p-6 text-white">
              <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="min-w-[260px]">
                  <div class="text-sm font-extrabold uppercase tracking-wide text-white/75">Live & Replays</div>

                  <div class="mt-2 text-xl md:text-2xl font-extrabold leading-snug">
                    {{ $title ? \Illuminate\Support\Str::limit($title, 90) : 'Fresh Fountain Service' }}
                  </div>

                  @if($prettyDate)
                    <div class="mt-2 text-white/80 font-semibold">{{ $prettyDate }}</div>
                  @endif
                </div>

                <a href="{{ $ytStreamsUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center rounded-xl bg-white text-slate-900 px-5 py-3 font-extrabold shadow-lg">
                  View all services →
                </a>
              </div>

              <div class="mt-3 text-white/80 leading-relaxed">
                {{ $isLive ? 'We are live right now — join the service.' : 'Catch up with the latest service and recent livestreams.' }}
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
</section>


{{-- =========================================================
  EVENTS (HeartChurch-style) — pulls from Eventib API
========================================================= --}}
@php
  use Illuminate\Support\Facades\Cache;
  use Illuminate\Support\Facades\Http;
  use Carbon\Carbon;

  // CONFIG
  $eventibBase = 'https://eventib.com';
  $organizerSlug = 'fresh-fountain';
  $limit = 6;

  // Optional API key
  $apiKey = env('EVENTIB_PUBLIC_FEED_KEY');

  $apiUrl = $eventibBase . '/api/public/organizers/' . $organizerSlug . '/events?limit=' . $limit;
  if ($apiKey) $apiUrl .= '&key=' . urlencode($apiKey);

  $feed = Cache::remember('ff_eventib_feed_' . $organizerSlug, now()->addMinutes(10), function () use ($apiUrl) {
      try {
          $res = Http::timeout(8)->get($apiUrl);
          if (!$res->ok()) return null;
          return $res->json();
      } catch (\Throwable $e) {
          return null;
      }
  });

  $events = data_get($feed, 'events', []);

  // helper: ensure banners are absolute urls
  $makeBannerUrl = function ($banner) use ($eventibBase) {
      if (!$banner) return null;

      $banner = trim((string) $banner);

      // already absolute
      if (preg_match('~^https?://~i', $banner)) return $banner;

      // already /storage/...
      if (str_starts_with($banner, '/storage/')) return $eventibBase . $banner;

      // banners/... or pages/... => assume in public storage
      return $eventibBase . '/storage/' . ltrim($banner, '/');
  };
@endphp

<section class="bg-white">
  <div id="event-section" class="max-w-[1400px] mx-auto px-4 py-16">

    <div class="flex items-end justify-between gap-6 flex-wrap">
      <div>
        <p class="text-sm font-extrabold uppercase tracking-wide text-slate-500">
          What’s On
        </p>
        <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-slate-900">
          Upcoming Events
        </h2>
        <p class="mt-2 text-slate-600 max-w-2xl">
          Join us this week — services, gatherings, and special events.
        </p>
      </div>

      <a href="{{ $eventibBase . '/organizers/' . $organizerSlug }}" target="_blank" rel="noopener"
         class="inline-flex items-center justify-center rounded-2xl bg-[rgb(var(--brand))] px-6 py-3 font-extrabold text-white
                hover:bg-[rgb(var(--brand-dark))] transition">
        View all events →
      </a>
    </div>

    @if(is_array($events) && count($events))
      <div class="mt-10 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @foreach($events as $e)
          @php
            $title = (string) data_get($e, 'title', 'Event');

            // ✅ NEW API shape (from your updated Eventib controller)
            $nextSessionDate = data_get($e, 'next_session.date');     // e.g. 2026-03-14
            $nextSessionName = (string) data_get($e, 'next_session.name', '');

            // ✅ Banner url normalization (prevents 404)
            $banner = $makeBannerUrl(data_get($e, 'banner'));

            $location = (string) data_get($e, 'location', '');
            $url = data_get($e, 'url') ?: ($eventibBase . '/events/' . (string) data_get($e, 'id'));

            $dt = $nextSessionDate ? Carbon::parse($nextSessionDate) : null;

            $day = $dt ? $dt->format('d') : '';
            $month = $dt ? strtoupper($dt->format('M')) : '';
            $dow = $dt ? strtoupper($dt->format('D')) : '';
          @endphp

          <a href="{{ $url }}" target="_blank" rel="noopener"
             class="group rounded-3xl bg-white border border-slate-200 overflow-hidden
                    shadow-[0_14px_40px_rgba(15,23,42,0.08)]
                    hover:shadow-[0_22px_70px_rgba(15,23,42,0.14)]
                    transition-all duration-300 hover:-translate-y-1">

            {{-- Image header --}}
            <div class="relative h-[220px] bg-slate-100 overflow-hidden">
              @if($banner)
                <img
                  src="{{ $banner }}"
                  alt="{{ $title }}"
                  loading="lazy"
                  class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700"
                >
              @else
                {{-- ✅ fallback if no banner --}}
                <div class="absolute inset-0 bg-gradient-to-tr from-[rgb(var(--brand))]/25 via-slate-900/20 to-transparent"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="text-white/90 text-center px-6">
                    <div class="text-xs font-extrabold uppercase tracking-widest opacity-80">Fresh Fountain</div>
                    <div class="mt-2 text-2xl font-extrabold leading-tight">
                      {{ \Illuminate\Support\Str::limit($title, 34) }}
                    </div>
                  </div>
                </div>
              @endif

              {{-- soft overlay --}}
              <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>

              {{-- session name + date pill --}}
              <div class="absolute top-4 left-4 right-4 flex items-start justify-between gap-4">
                <div class="text-white font-extrabold tracking-wide uppercase text-sm drop-shadow">
                  {{ \Illuminate\Support\Str::limit($nextSessionName ?: 'Upcoming', 32) }}
                </div>

                <div class="shrink-0 rounded-full bg-white shadow-lg border border-slate-100 w-[64px] h-[64px]
                            flex flex-col items-center justify-center text-center">
                  <div class="text-[10px] font-extrabold text-slate-500 -mb-1">{{ $dow }}</div>
                  <div class="text-xl font-extrabold text-slate-900 leading-none">{{ $day }}</div>
                  <div class="text-[10px] font-extrabold text-slate-500">{{ $month }}</div>
                </div>
              </div>
            </div>

            {{-- Body --}}
            <div class="p-6">
              <h3 class="text-lg font-extrabold text-slate-900 leading-snug">
                {{ $title }}
              </h3>

              <div class="mt-4 space-y-2 text-sm text-slate-600">
                @if($dt)
                  <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100">🗓️</span>
                    <span class="font-semibold">{{ $dt->format('jS F, Y') }}</span>
                  </div>
                @endif

                @if($location)
                  <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100">📍</span>
                    <span class="font-semibold">{{ \Illuminate\Support\Str::limit($location, 48) }}</span>
                  </div>
                @endif
              </div>

              <div class="mt-6 inline-flex items-center gap-2 font-extrabold text-[rgb(var(--brand))]">
                View details <span class="transition group-hover:translate-x-1">→</span>
              </div>
            </div>

          </a>
        @endforeach
      </div>
    @else
      <div class="mt-10 rounded-3xl border border-slate-200 bg-slate-50 p-10 text-slate-700">
        No upcoming events available right now.
      </div>
    @endif

  </div>
</section>

<!--
{{-- =========================================================
  WHAT’S ON / EVENTS (uses services.items)
========================================================= --}}
<section id="whats-on" class="bg-slate-50">
  <div class="max-w-[1400px] mx-auto px-4 py-16">
    <div class="flex items-end justify-between gap-6 flex-wrap">
      <div>
        <p class="text-sm font-extrabold uppercase tracking-wide text-[rgb(var(--brand))]">What’s on</p>
        <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-slate-900">
          {{ $servicesTitle }}
        </h2>
        <p class="mt-3 text-slate-600 max-w-2xl">
          {{ data_get($page->sections, 'services.subtitle', 'Upcoming gatherings, programmes and community moments.') }}
        </p>
      </div>
    </div>

    <div class="mt-10 grid gap-6 md:grid-cols-3">
      @foreach($services as $svc)
        @php
          $svcImg = data_get($svc, 'image');
          $svcImgUrl = $svcImg ? asset('storage/' . $svcImg) : null;
          $svcTitle = data_get($svc, 'title', 'Event');
          $svcDesc  = data_get($svc, 'description', '');
          $svcLink  = data_get($svc, 'link', '/contact');
        @endphp

        <a href="{{ $svcLink }}"
           class="group block rounded-[34px] overflow-hidden border border-slate-200 bg-white shadow-[0_14px_40px_rgba(15,23,42,0.10)]
                  hover:shadow-[0_22px_70px_rgba(15,23,42,0.18)] transition-all duration-300 hover:-translate-y-1">

          <div class="relative aspect-[16/10] bg-slate-100 overflow-hidden">
            @if($svcImgUrl)
              <img src="{{ $svcImgUrl }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700" alt="">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            <div class="absolute bottom-4 left-4 right-4">
              <div class="inline-flex rounded-full bg-white/15 border border-white/15 px-4 py-2 text-white font-extrabold">
                {{ $svcTitle }}
              </div>
            </div>
          </div>

          <div class="p-7">
            <p class="text-slate-600 leading-relaxed">
              {{ Str::limit($svcDesc, 140) }}
            </p>
            <div class="mt-5 inline-flex items-center gap-2 font-extrabold text-[rgb(var(--brand))]">
              View details <span class="transition group-hover:translate-x-1">→</span>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>

{{-- =========================================================
  BIG CHURCH CTA — “Come Worship / Give / Connect”
========================================================= --}}
<section class="bg-white">
  <div class="max-w-[1400px] mx-auto px-4 py-16">
    <div class="relative overflow-hidden rounded-[44px] bg-[rgb(var(--navy))] text-white p-10 md:p-14 shadow-[0_22px_70px_rgba(15,23,42,0.25)]">
      <div class="absolute inset-0 opacity-70 bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.12),transparent_55%)]"></div>
      <div class="absolute -right-40 -top-40 h-[420px] w-[420px] rounded-full bg-[rgba(var(--brand),0.26)] blur-3xl"></div>
      <div class="absolute -left-40 -bottom-40 h-[420px] w-[420px] rounded-full bg-white/10 blur-3xl"></div>

      <div class="relative grid gap-10 lg:grid-cols-[1.4fr_1fr] lg:items-center">
        <div>
          <p class="text-sm font-extrabold uppercase tracking-wide text-white/75">
            {{ data_get($cta, 'kicker', 'Get involved') }}
          </p>
          <h3 class="mt-3 text-3xl md:text-5xl font-extrabold">
            {{ data_get($cta, 'title', 'You belong here') }}
          </h3>
          <p class="mt-4 text-white/85 leading-relaxed max-w-2xl">
            {{ data_get($cta, 'subtitle', 'Whether you’re visiting for the first time, looking for community, or ready to serve — we’d love to connect.') }}
          </p>

          <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ data_get($cta, 'button_link', '/#visit') }}"
               class="inline-flex items-center justify-center rounded-2xl bg-[rgb(var(--brand))] px-7 py-3 font-extrabold hover:bg-[rgb(var(--brand-dark))] transition">
              {{ data_get($cta, 'button_text', 'Plan a Visit') }}
            </a>
            <a href="{{ data_get($cta, 'secondary_button_link', '/giving') }}"
               class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-7 py-3 font-extrabold hover:bg-white/10 transition">
              {{ data_get($cta, 'secondary_button_text', 'Give') }}
            </a>
            <a href="/contact"
               class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-black/30 px-7 py-3 font-extrabold hover:bg-black/40 transition">
              Contact us
            </a>
          </div>
        </div>

        <div class="grid gap-3">
          @php
            $ctaPoints = data_get($page->sections, 'cta_points.items', []);
            if (!is_array($ctaPoints) || !count($ctaPoints)) {
              $ctaPoints = [
                ['title' => 'New here?', 'text' => 'We’ll help you plan your first visit.'],
                ['title' => 'Need prayer?', 'text' => 'Send a request — we’ll stand with you.'],
                ['title' => 'Want to serve?', 'text' => 'Join a team and make a difference.'],
              ];
            }
          @endphp

          @foreach($ctaPoints as $p)
            <div class="rounded-3xl bg-white/10 border border-white/10 backdrop-blur p-5">
              <div class="font-extrabold">{{ data_get($p, 'title', '') }}</div>
              <div class="mt-1 text-white/85">{{ data_get($p, 'text', '') }}</div>
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</section>

{{-- =========================================================
  BLOG — keep it simple
========================================================= --}}
<section class="bg-slate-50">
  <div class="max-w-[1400px] mx-auto px-4 py-16">
    <div class="flex items-end justify-between gap-6 flex-wrap">
      <div>
        <p class="text-sm font-extrabold uppercase tracking-wide text-[rgb(var(--brand))]">Latest</p>
        <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-slate-900">
          {{ $blogTitle }}
        </h2>
      </div>

      <a href="{{ $blogButtonLink }}"
         class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 font-extrabold text-slate-900 hover:bg-slate-100 transition">
        {{ $blogButtonText }}
      </a>
    </div>

    <div class="mt-10 grid gap-6 md:grid-cols-3">
      @forelse($latestPosts as $post)
        @php
          $imgUrl = $post->featured_image ? asset('storage/' . $post->featured_image) : null;
          $link = url('/blog/' . $post->slug);
        @endphp

        <a href="{{ $link }}"
           class="group block overflow-hidden rounded-[34px] bg-white border border-slate-200
                  shadow-[0_14px_40px_rgba(15,23,42,0.10)] hover:shadow-[0_22px_70px_rgba(15,23,42,0.18)]
                  transition-all duration-300 hover:-translate-y-1">

          <div class="aspect-[16/10] bg-slate-100 overflow-hidden">
            @if($imgUrl)
              <img src="{{ $imgUrl }}" class="h-full w-full object-cover group-hover:scale-105 transition duration-700" alt="{{ $post->title }}">
            @endif
          </div>

          <div class="p-7">
            <div class="text-xs font-extrabold text-[rgb(var(--brand))] uppercase tracking-wide">
              {{ optional($post->published_at)->format('d M Y') }}
            </div>

            <h3 class="mt-2 font-extrabold text-slate-900 leading-snug text-lg">
              {{ $post->title }}
            </h3>

            @if($post->excerpt)
              <p class="mt-3 text-slate-600 leading-relaxed">
                {{ Str::limit($post->excerpt, 140) }}
              </p>
            @endif

            <div class="mt-4 inline-flex items-center gap-2 text-[rgb(var(--brand))] font-extrabold text-sm">
              Read More <span class="transition group-hover:translate-x-1">→</span>
            </div>
          </div>
        </a>
      @empty
        <div class="rounded-[34px] border border-slate-200 bg-white p-10 text-slate-600 md:col-span-3">
          No posts yet.
        </div>
      @endforelse
    </div>
  </div>
</section>

{{-- =========================================================
  LOGOS
========================================================= --}}
<section class="bg-white">
  <div class="max-w-[1400px] mx-auto px-4 py-12">
    <div class="flex flex-wrap items-center justify-center gap-10">
      @foreach($logos as $l)
        @php
          $li = data_get($l, 'image');
          $liUrl = $li ? asset('storage/' . $li) : null;
        @endphp

        @if($liUrl)
          <a href="{{ data_get($l, 'link', '#') }}"
             class="opacity-80 hover:opacity-100 transition"
             target="_blank" rel="noopener">
            <img src="{{ $liUrl }}" class="h-14 md:h-16 lg:h-20 object-contain" alt="">
          </a>
        @endif
      @endforeach
    </div>
  </div>
</section> -->
@php
  use Illuminate\Support\Facades\Storage;

  // Pull banner image from Contact page (so you don't upload a new CTA image)
  $contactPage = \App\Models\Page::where('slug', 'contact')->first()
      ?? \App\Models\Page::where('slug', 'contact')->first();

  $contactBanner = null;

  if ($contactPage && $contactPage->banner_image) {
      $contactBanner = Storage::url($contactPage->banner_image);
  }
@endphp

{{-- =========================================================
  CONTACT CTA — full width, uses Contact page banner
========================================================= --}}
<section class="relative w-full overflow-hidden bg-slate-950">
  @if($contactBanner)
    <img
      src="{{ $contactBanner }}"
      class="absolute inset-0 w-full h-full object-cover"
      alt="Contact Fresh Fountain">
  @endif

  {{-- overlays --}}
  <div class="absolute inset-0 bg-black/55"></div>
  <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/20"></div>

  <div class="relative max-w-[1400px] mx-auto px-6 py-24 md:py-32">
    <div class="max-w-2xl text-white">
      <p class="text-sm font-extrabold uppercase tracking-wide text-white/70">
        Contact us
      </p>

      <h2 class="mt-4 text-4xl md:text-6xl font-serif font-semibold leading-tight">
        We’d love to hear from you
      </h2>

      <p class="mt-6 text-lg text-white/85 leading-relaxed">
        If you’d like to find out more about church, let us know you’re visiting,
        or simply have a question — please don’t hesitate to get in touch.
      </p>

      <div class="mt-10">
        <a href="/contact"
           class="inline-flex items-center justify-center rounded-xl bg-white px-8 py-4
                  text-slate-900 font-extrabold hover:bg-slate-100 transition">
          Get in touch →
        </a>
      </div>
    </div>
  </div>
</section>



@endsection
