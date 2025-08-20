@extends('layouts.app')

@section('content')
  {{-- HERO --}}
  <section class="relative overflow-hidden rounded-2xl mb-10 bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600">
    <div class="relative p-8 md:p-12 lg:p-16 text-white">
      <div class="max-w-3xl">
        <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight leading-tight">
          Find your next phone at PhoneHub
        </h1>
        <p class="mt-3 text-white/90 text-base md:text-lg">
          Thousands of ads from private sellers. Search, compare, buy.
        </p>

        {{-- Αναζήτηση (μόνο q) --}}
        <form action="{{ route('listings.index') }}" method="GET" class="mt-6">
          <div class="flex items-stretch gap-2 bg-white rounded-xl p-1">
            <div class="flex items-center px-3 text-gray-500">🔎</div>
            <input
              name="q"
              value="{{ request('q') }}"
              class="flex-1 rounded-lg px-3 py-3 text-gray-900 focus:outline-none"
              placeholder="e.g. iPhone 13 Pro, Galaxy S24, Pixel 8..."
            />
            <button
              class="px-5 py-3 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
              Search
            </button>
          </div>
        </form>

        {{-- Quick filters (προδημοφιλή) --}}
        <div class="mt-4 flex flex-wrap gap-2">
          @php
            $quick = [
              ['label'=>'Apple','params'=>['brand'=>'Apple']],
              ['label'=>'Samsung','params'=>['brand'=>'Samsung']],
              ['label'=>'Xiaomi','params'=>['brand'=>'Xiaomi']],
              ['label'=>'Google','params'=>['brand'=>'Google']],
              ['label'=>'OnePlus','params'=>['brand'=>'OnePlus']],
            ];
          @endphp
          @foreach($quick as $qf)
            <a href="{{ route('listings.index', $qf['params']) }}"
               class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/15 hover:bg-white/25 text-white text-sm transition">
              {{ $qf['label'] }}
            </a>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Διακοσμητικά blobs --}}
    <div class="pointer-events-none absolute -right-20 -bottom-24 w-96 h-96 rounded-full bg-white/10 blur-3xl"></div>
    <div class="pointer-events-none absolute right-10 -top-16 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
  </section>

  {{-- LATEST --}}
  <div class="flex items-center justify-between mb-4">
    <h2 class="font-semibold text-xl">Latest ads</h2>
    <a href="{{ route('listings.index') }}" class="text-sm text-blue-700 hover:underline">See all</a>
  </div>

  @if($latest->isEmpty())
    <div class="bg-white border rounded-xl p-6 text-gray-600">
      There are no ads yet.
      @auth
        <a href="{{ route('listings.create') }}" class="ml-2 text-blue-700 hover:underline">Post your first ad</a>
      @endauth
    </div>
  @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      @foreach ($latest as $l)
        <a href="{{ route('listings.show', $l) }}"
           class="group bg-white rounded-xl border overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition">
          <div class="relative">
            <img
              class="w-full aspect-[4/3] object-cover"
              src="{{ $l->primaryImage ? Storage::url($l->primaryImage->path) : 'https://via.placeholder.com/640x480?text=Phone' }}"
              alt="{{ $l->title }}"
              loading="lazy"
            >
            {{-- Brand badge --}}
            @if($l->brand)
              <div class="absolute top-2 left-2">
                <span class="px-2 py-1 text-xs rounded-full bg-black/70 text-white">
                  {{ $l->brand }}
                </span>
              </div>
            @endif
            {{-- Light overlay on hover --}}
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition"></div>
          </div>

          <div class="p-3">
            <div class="font-semibold leading-tight line-clamp-2">
              {{ $l->title }}
            </div>

            <div class="mt-1 text-sm text-gray-600 flex items-center justify-between">
              <span class="truncate">
                {{ $l->model }} @if($l->city) • {{ $l->city }} @endif
              </span>
              @if(!empty($l->published_at))
                <span class="shrink-0 text-gray-400 ml-2">
                  {{ \Illuminate\Support\Carbon::parse($l->published_at)->diffForHumans(null, true) }} ago
                </span>
              @endif
            </div>

            <div class="mt-2 flex items-center justify-between">
              <div class="text-lg font-extrabold">€{{ number_format($l->price, 2) }}</div>
              <div class="flex gap-1">
                @if($l->os)
                  <span class="px-2 py-0.5 text-xs rounded bg-gray-100">{{ $l->os }}</span>
                @endif
                @if($l->condition)
                  <span class="px-2 py-0.5 text-xs rounded bg-gray-100">{{ $l->condition }}</span>
                @endif
              </div>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  @endif

 
@endsection
