@extends('layouts.site')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;

  $banner = $page->banner_image ? Storage::url($page->banner_image) : null;

  $leaders   = data_get($page->sections, 'leaders', []);
  $pastors   = data_get($leaders, 'pastors', []);
  $trustees  = data_get($leaders, 'trustees', []);
  $ministers = data_get($leaders, 'ministers', []);
  $unitHeads = data_get($leaders, 'unit_heads', []);

  // ✅ Bigger, clearer circular images (responsive)
  // Mobile: 160x160, Desktop+: 192x192
  $renderGroup = function (string $title, array $items) {
      $safeItems = is_array($items) ? $items : [];
      if (!count($safeItems)) return;

      echo '<section class="bg-white">';
      echo '  <div class="max-w-[1400px] mx-auto px-4 py-14">';
      echo '    <div class="flex items-end justify-between gap-6 flex-wrap">';
      echo '      <div>';
      echo '        <p class="text-sm font-extrabold uppercase tracking-wide text-slate-500">Leadership</p>';
      echo '        <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-slate-900">'.e($title).'</h2>';
      echo '      </div>';
      echo '    </div>';

      echo '    <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">';
      foreach ($safeItems as $p) {
          $img      = data_get($p, 'image');
          $imgUrl   = $img ? asset('storage/' . ltrim($img, '/')) : null;

          $name     = (string) data_get($p, 'name', '');
          $position = (string) data_get($p, 'position', '');
          $unit     = (string) data_get($p, 'unit', '');

          echo '      <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-[0_14px_40px_rgba(15,23,42,0.08)] hover:shadow-[0_22px_70px_rgba(15,23,42,0.12)] transition">';
          echo '        <div class="flex flex-col items-center">';

          // ✅ BIG AVATAR
          echo '          <div class="mb-6">';
          echo '            <div class="h-80 w-80 md:h-68 md:w-68 rounded-full overflow-hidden ring-4 ring-white shadow-[0_18px_55px_rgba(15,23,42,0.22)] bg-slate-100">';
          if ($imgUrl) {
              echo '              <img src="'.e($imgUrl).'" alt="'.e($name).'" class="h-full w-full object-cover">';
          } else {
              echo '              <div class="h-full w-full bg-slate-200"></div>';
          }
          echo '            </div>';
          echo '          </div>';

          // Name
          echo '          <div class="text-xl md:text-2xl font-extrabold text-slate-900">'.e($name).'</div>';

          // Position pill
          if ($position) {
              echo '          <div class="mt-2 inline-flex items-center rounded-full bg-[rgb(var(--brand))]/10 px-4 py-1.5 text-xs font-extrabold text-[rgb(var(--brand))]">'
                  .e($position).'</div>';
          }

          // Unit / role text
          if ($unit) {
              echo '          <div class="mt-4 text-sm font-semibold text-slate-600">'.e($unit).'</div>';
          }

          echo '        </div>';
          echo '      </div>';
      }
      echo '    </div>';

      echo '  </div>';
      echo '</section>';
  };
@endphp

{{-- =========================================================
  HERO — Fresh Fountain style
========================================================= --}}
<section class="relative overflow-hidden bg-[rgb(var(--dark))] text-white">
  @if($banner)
    <img src="{{ $banner }}" class="absolute inset-0 w-full h-full object-cover opacity-35" alt="{{ $page->title }}">
  @endif

  <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/25"></div>

  <div class="relative max-w-[1400px] mx-auto px-4 py-20 md:py-28">
    <div class="max-w-3xl">
      <p class="text-sm font-extrabold uppercase tracking-wide text-white/70">
        About
      </p>

      <h1 class="mt-3 text-4xl md:text-6xl font-extrabold leading-tight">
        {{ $page->title ?? 'Leaders' }}
      </h1>

      @if($page->excerpt)
        <p class="mt-6 text-lg md:text-xl text-white/80 leading-relaxed max-w-2xl">
          {{ $page->excerpt }}
        </p>
      @endif
    </div>
  </div>
</section>

{{-- =========================================================
  LEADERS GROUPS
========================================================= --}}
@php
  $renderGroup('Pastors', is_array($pastors) ? $pastors : []);
  $renderGroup('Trustees', is_array($trustees) ? $trustees : []);
  $renderGroup('Ministers', is_array($ministers) ? $ministers : []);
  $renderGroup('Unit Heads', is_array($unitHeads) ? $unitHeads : []);
@endphp

@endsection
