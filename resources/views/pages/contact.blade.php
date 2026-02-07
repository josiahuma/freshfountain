@extends('layouts.site')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;

  // Page banner (uploaded in CMS for THIS contact page)
  $banner = $page->banner_image ? Storage::url($page->banner_image) : null;

  // Pull service times from HOME (optional)
  $home = \App\Models\Page::where('slug','home')->first();
  $homeSections = $home?->sections ?? [];
  if (is_string($homeSections)) $homeSections = json_decode($homeSections, true) ?: [];

  $times = data_get($homeSections, 'service_times.items', []);
  if (!is_array($times) || !count($times)) {
      $times = [
        ['title' => 'Sunday Encounter', 'text' => '10:30 AM'],
        ['title' => 'Tuesday Overflow (Online Bible Study)', 'text' => 'Tuesday • 7:00 PM'],
        ['title' => 'Refresh (Worship & Miracle Service)', 'text' => '1st Fridays • 7:00 PM'],
      ];
  }

  // Contact fields from THIS page sections
  $contact   = data_get($page->sections, 'contact', []);
  $address   = data_get($contact, 'address');
  $phone1    = data_get($contact, 'phone_primary');
  $phone2    = data_get($contact, 'phone_secondary');
  $email     = data_get($contact, 'email');
  $formTitle = data_get($contact, 'form_title', 'Send us a message');
  $mapEmbed  = data_get($contact, 'map_embed');
@endphp

<style>
  .ff-soft { box-shadow: 0 18px 55px rgba(15,23,42,.10); }
  .ff-glow { box-shadow: 0 30px 90px rgba(0,0,0,.35); }
</style>

{{-- =========================================================
  HERO — Church Contact
========================================================= --}}
<section class="relative overflow-hidden bg-slate-950 text-white">
  @if($banner)
    <img src="{{ $banner }}" class="absolute inset-0 w-full h-full object-cover opacity-40" alt="">
  @endif

  {{-- warm church overlay --}}
  <div class="absolute inset-0 bg-gradient-to-br from-black/80 via-black/55 to-[rgb(var(--brand))]/20"></div>

  <div class="relative max-w-[1400px] mx-auto px-4 py-20 md:py-28">
    <div class="max-w-3xl">
      <div class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/10 px-4 py-2 text-xs md:text-sm font-extrabold uppercase tracking-wide">
        Contact Fresh Fountain
      </div>

      <h1 class="mt-6 text-4xl md:text-6xl font-serif font-semibold leading-tight">
        {{ $page->title ?? 'Contact Us' }}
      </h1>

      <p class="mt-6 text-lg md:text-xl text-white/80 leading-relaxed max-w-2xl">
        {{ $page->excerpt ?: 'If you’re new, visiting, or you need prayer — we are here for you. Every message is read and prayed over.' }}
      </p>

      <div class="mt-10 flex flex-wrap gap-3">
        <a href="#contact-form"
           class="inline-flex items-center justify-center rounded-xl bg-white px-7 py-3 font-extrabold text-slate-900 hover:bg-slate-100 transition">
          Send a message →
        </a>

        <a href="#map"
           class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 px-7 py-3 font-extrabold text-white hover:bg-white/15 transition">
          Plan a visit
        </a>

      </div>
    </div>
  </div>
</section>

{{-- =========================================================
  MAIN — Info + Form
========================================================= --}}
<section class="bg-white">
  <div class="max-w-[1400px] mx-auto px-4 py-16">
    <div class="grid gap-10 lg:grid-cols-12 lg:items-start">

      {{-- LEFT --}}
      <div class="lg:col-span-5 space-y-6">

        {{-- Warm intro card --}}
        <div class="rounded-[34px] border border-slate-200 bg-slate-50 p-8 ff-soft">
          <p class="text-sm font-extrabold uppercase tracking-wide text-[rgb(var(--brand))]">We’re here for you</p>
          <h2 class="mt-2 text-2xl md:text-3xl font-extrabold text-slate-900">Let’s connect</h2>
          <p class="mt-3 text-slate-600 leading-relaxed">
            Whether you have a question, want to visit, or need prayer — reach out anytime.
          </p>

          {{-- Contact blocks --}}
          <div class="mt-7 space-y-4">

            @if($address)
              <div class="rounded-3xl bg-white border border-slate-200 p-6">
                <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wide">Where to find us</div>
                <div class="mt-2 whitespace-pre-line text-slate-800 font-semibold leading-relaxed">
                  {{ $address }}
                </div>
              </div>
            @endif

            @if($phone1 || $phone2)
              <div class="rounded-3xl bg-white border border-slate-200 p-6">
                <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wide">Call us</div>
                <div class="mt-2 space-y-1 text-slate-800 font-extrabold">
                  @if($phone1)
                    <a class="hover:text-[rgb(var(--brand))] transition" href="tel:{{ preg_replace('/\s+/', '', $phone1) }}">
                      {{ $phone1 }}
                    </a>
                  @endif
                  @if($phone2)
                    <div>
                      <a class="hover:text-[rgb(var(--brand))] transition" href="tel:{{ preg_replace('/\s+/', '', $phone2) }}">
                        {{ $phone2 }}
                      </a>
                    </div>
                  @endif
                </div>
              </div>
            @endif

            @if($email)
              <div class="rounded-3xl bg-white border border-slate-200 p-6">
                <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wide">Email</div>
                <a class="mt-2 block font-extrabold text-[rgb(var(--brand))] hover:opacity-80 transition"
                   href="mailto:{{ $email }}">
                  {{ $email }}
                </a>
              </div>
            @endif

            {{-- Prayer tone --}}
            <div class="rounded-3xl bg-[rgb(var(--brand))]/10 border border-[rgb(var(--brand))]/20 p-6">
              <div class="text-xs font-extrabold uppercase tracking-wide text-slate-700">A note from us</div>
              <div class="mt-2 text-slate-900 font-semibold leading-relaxed">
                Every message is read and prayed over. You’re not alone.
              </div>
            </div>

          </div>
        </div>

        {{-- Service times --}}
        <div class="rounded-[34px] border border-slate-200 bg-white p-8 ff-soft">
          <div class="flex items-end justify-between gap-4">
            <div>
              <p class="text-sm font-extrabold uppercase tracking-wide text-[rgb(var(--brand))]">Service times</p>
              <h3 class="mt-2 text-xl font-extrabold text-slate-900">Join us</h3>
            </div>
            <a href="/#visit" class="text-sm font-extrabold text-[rgb(var(--brand))] hover:opacity-80 transition">
              Plan a visit →
            </a>
          </div>

          <div class="mt-6 grid gap-3">
            @foreach($times as $t)
              <div class="rounded-3xl bg-slate-50 border border-slate-200 p-5">
                <div class="font-extrabold text-slate-900">{{ data_get($t, 'title', 'Service') }}</div>
                <div class="mt-2 font-extrabold text-[rgb(var(--brand))]">
                  {{ data_get($t, 'text', '') }}
                </div>
              </div>
            @endforeach
          </div>
        </div>

      </div>

      {{-- RIGHT: FORM --}}
      <div class="lg:col-span-7">
        <div id="contact-form" class="rounded-[36px] bg-white border border-slate-200 ff-soft p-8 md:p-10">
          <p class="text-sm font-extrabold uppercase tracking-wide text-[rgb(var(--brand))]">Send a message</p>
          <h2 class="mt-2 text-2xl md:text-4xl font-extrabold text-slate-900">{{ $formTitle }}</h2>
          <p class="mt-3 text-slate-600 leading-relaxed max-w-2xl">
            Tell us what’s on your heart — a question, a visit, or a prayer request.
          </p>

          {{-- gentle highlight --}}
          <div class="mt-6 rounded-3xl bg-slate-50 border border-slate-200 p-5">
            <div class="font-semibold text-slate-900">Need prayer?</div>
            <div class="mt-1 text-slate-600">You can include your prayer request in the message below.</div>
          </div>

          {{-- UI-only form (wire backend later) --}}
          <form class="mt-8 grid gap-5 md:grid-cols-2">
            <div>
              <label class="text-sm font-extrabold text-slate-700">Name</label>
              <input
                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-semibold text-slate-900
                       focus:outline-none focus:ring-2 focus:ring-[rgb(var(--brand))]/25"
                placeholder="Your full name"
              />
            </div>

            <div>
              <label class="text-sm font-extrabold text-slate-700">Email</label>
              <input
                type="email"
                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-semibold text-slate-900
                       focus:outline-none focus:ring-2 focus:ring-[rgb(var(--brand))]/25"
                placeholder="you@example.com"
              />
            </div>

            <div class="md:col-span-2">
              <label class="text-sm font-extrabold text-slate-700">Phone (optional)</label>
              <input
                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-semibold text-slate-900
                       focus:outline-none focus:ring-2 focus:ring-[rgb(var(--brand))]/25"
                placeholder="Optional"
              />
            </div>

            <div class="md:col-span-2">
              <label class="text-sm font-extrabold text-slate-700">Message / Prayer request</label>
              <textarea
                rows="7"
                class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-semibold text-slate-900
                       focus:outline-none focus:ring-2 focus:ring-[rgb(var(--brand))]/25"
                placeholder="Write your message here..."
              ></textarea>
            </div>

            <div class="md:col-span-2 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div class="text-sm text-slate-600">
                By submitting, you agree we may contact you regarding your message.
              </div>

              <button type="button"
                class="inline-flex items-center justify-center rounded-2xl bg-[rgb(var(--brand))] px-8 py-3 text-white font-extrabold
                       hover:opacity-90 transition">
                Send message →
              </button>
            </div>
          </form>

          @if($page->content)
            <div class="mt-10 prose prose-slate max-w-none">
              {!! $page->content !!}
            </div>
          @endif
        </div>
      </div>

    </div>
  </div>
</section>

{{-- =========================================================
  MAP — Clean panel
========================================================= --}}
@if($mapEmbed)
<section class="bg-slate-50 border-t border-slate-100">
  <div id="map" class="max-w-[1400px] mx-auto px-4 py-14">
    <div class="rounded-[34px] overflow-hidden border border-slate-200 bg-white ff-soft">
      <div class="aspect-[16/7]">
        {!! $mapEmbed !!}
      </div>
    </div>
  </div>
</section>
@endif
@endsection
