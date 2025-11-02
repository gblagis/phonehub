{{-- resources/views/listings/show.blade.php --}}

@extends('layouts.app')

@section('content')
    @php
        // Μεταβλητές διακομιστή για την προβολή (avatar, ιδιοκτησία, εξουσιοδότηση, τηλέφωνο/email)

        
        $rawImages = $listing->images ?? [];
        $photos = collect($rawImages)
            ->map(function ($p) {
                $path = is_array($p)
                    ? $p['path'] ?? ($p['url'] ?? null)
                    : (is_object($p)
                        ? $p->path ?? ($p->url ?? null)
                        : (is_string($p)
                            ? $p
                            : null));
                return $path ? Storage::url($path) : null;
            })
            ->filter()
            ->values();
        $photosCount = $photos->count();

        $user = optional($listing->user);
        $avatarPath = $user->avatar_path ?? ($user->avatar ?? null);
        $avatarUrl = $avatarPath ? Storage::url($avatarPath) : 'https://via.placeholder.com/80?text=U';

        $isOwner = auth()->check() && auth()->id() === $listing->user_id;
        $isAuth = auth()->check();

        // Phone: show 10-digit as 3-3-4 (e.g., 698 758 7471) + tel: href
        $rawPhone = $listing->contact_phone;
        $digits = $rawPhone ? preg_replace('/\D+/', '', $rawPhone) : null;
        $raw10 = $digits ? substr($digits, -10) : null;
        $phoneNice = $raw10 ? substr($raw10, 0, 3) . ' ' . substr($raw10, 3, 3) . ' ' . substr($raw10, 6, 4) : null;
        $phoneHref = $raw10 ? 'tel:+30' . $raw10 : '#';

        // Email to contact (prefer listing's contact_email, fallback to user's)
        $displayEmail = $listing->contact_email ?: $user->email;

        // Prefill fields for email modal
        $prefillName = old('name', optional(auth()->user())->name);
        $prefillEmail = old('email', optional(auth()->user())->email);
        $prefillMsg = old('message') ?: "I'm interested in your listing {$listing->title}. Please contact me.";
    @endphp

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-50 text-red-700 border border-red-200">
            @foreach ($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <article class="grid lg:grid-cols-3 gap-8">
        {{-- LEFT: Gallery + (Device details inline under gallery) + Title + Description --}}
        <div class="lg:col-span-2">
            {{-- Gallery --}}
            <div class="relative bg-white rounded-xl shadow overflow-hidden">
                <div class="relative aspect-[4/3] bg-gray-100 md:max-h-[55vh] max-h-[50vh]">
                    @if ($photosCount)
                        <img id="gallery-main" src="{{ $photos->first() }}" data-index="0"
                            class="w-full h-full object-contain select-none" alt="{{ $listing->title }}" />
                    @else
                        <div class="w-full h-full grid place-items-center text-gray-400 text-sm">No images</div>
                    @endif

                    {{-- ΠΑΝΤΑ να αποδίδονται τα βέλη όταν υπάρχει τουλάχιστον μία εικόνα (ακόμα κι αν είναι μόνο 1) --}}
                    @if ($photosCount)
                        {{-- Στοιχεία ελέγχου επικάλυψης - απόλυτα τοποθετημένα, δεν επηρεάζουν τη διάταξη --}}
                        <div class="pointer-events-none absolute inset-0 flex items-center justify-between px-2">
                            <button id="gallery-prev" aria-label="Previous"
                                class="pointer-events-auto z-20
                           w-10 h-10 md:w-12 md:h-12 grid place-items-center
                           rounded-full bg-gray-200/90 hover:bg-gray-300
                           text-3xl md:text-4xl leading-none text-gray-800 shadow ring-1 ring-black/5">
                                ‹
                            </button>
                            <button id="gallery-next" aria-label="Next"
                                class="pointer-events-auto z-20
                           w-10 h-10 md:w-12 md:h-12 grid place-items-center
                           rounded-full bg-gray-200/90 hover:bg-gray-300
                           text-3xl md:text-4xl leading-none text-gray-800 shadow ring-1 ring-black/5">
                                ›
                            </button>
                        </div>
                    @endif
                </div>

                @if ($photosCount > 1)
                    <div id="gallery-thumbs" class="grid grid-cols-5 gap-2 p-3 border-t">
                        @foreach ($photos as $i => $url)
                            <button type="button" class="group relative" data-index="{{ $i }}"
                                data-src="{{ $url }}">
                                <img src="{{ $url }}"
                                    class="h-16 md:h-20 w-full object-cover rounded border group-data-[active=true]:ring-2 group-data-[active=true]:ring-blue-600"
                                    alt="thumb {{ $i + 1 }}" loading="lazy" />
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Device details inline under gallery --}}
                <div class="p-3 border-t text-sm">
                    <h3 class="font-semibold mb-2">Device details</h3>
                    <dl class="grid grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-2">
                        @if ($listing->brand)
                            <div>
                                <dt class="text-gray-500">Brand</dt>
                                <dd class="font-medium">{{ $listing->brand }}</dd>
                            </div>
                        @endif
                        @if ($listing->model)
                            <div>
                                <dt class="text-gray-500">Model</dt>
                                <dd class="font-medium">{{ $listing->model }}</dd>
                            </div>
                        @endif
                        @if ($listing->year)
                            <div>
                                <dt class="text-gray-500">Year</dt>
                                <dd class="font-medium">{{ $listing->year }}</dd>
                            </div>
                        @endif
                        @if ($listing->os)
                            <div>
                                <dt class="text-gray-500">OS</dt>
                                <dd class="font-medium">{{ $listing->os }}</dd>
                            </div>
                        @endif
                        @if ($listing->condition)
                            <div>
                                <dt class="text-gray-500">Condition</dt>
                                <dd class="font-medium">{{ $listing->condition }}</dd>
                            </div>
                        @endif
                        @if ($listing->color)
                            <div>
                                <dt class="text-gray-500">Color</dt>
                                <dd class="font-medium">{{ $listing->color }}</dd>
                            </div>
                        @endif
                        @if ($listing->city)
                            <div>
                                <dt class="text-gray-500">City</dt>
                                <dd class="font-medium">{{ $listing->city }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Title + Description (price lives on the right panel) --}}
            <div class="mt-6">
                <div class="flex items-start justify-between gap-4">
                    <h1 class="text-2xl font-display">{{ $listing->title }}</h1>
                    @if ($isOwner)
                        <a href="{{ route('listings.edit', ['listing' => $listing->id]) }}"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-yellow-500 text-white text-sm font-semibold hover:bg-yellow-600">
                            ✎ Edit
                        </a>
                    @endif
                </div>
                <p class="mt-4 whitespace-pre-line">{{ $listing->description }}</p>
            </div>

            {{-- Lightbox --}}
            <div id="lightbox" class="fixed inset-0 z-50 hidden">
                <div id="lb-backdrop" class="absolute inset-0 bg-black/80 z-10"></div>
                <div id="lb-stage"
                    class="absolute inset-0 z-20 flex items-center justify-center overflow-hidden touch-pan-y">
                    <img id="lb-img" src="" alt="preview"
                        class="max-h-[90vh] max-w-[90vw] select-none cursor-zoom-in" />
                </div>
                <button id="lb-close"
                    class="absolute top-4 right-4 z-30 bg-white/90 rounded-full px-3 py-1 text-black shadow"
                    aria-label="Close">✕</button>
                <button id="lb-prev"
                    class="absolute left-4 top-1/2 -translate-y-1/2 z-30 bg-gray-200/90 rounded-full px-3 py-2 shadow text-5xl font-bold">‹</button>
                <button id="lb-next"
                    class="absolute right-4 top-1/2 -translate-y-1/2 z-30 bg-gray-200/90 rounded-full px-3 py-2 shadow text-5xl font-bold">›</button>
                <div id="lb-counter" class="absolute bottom-4 left-1/2 -translate-x-1/2 z-30 text-white/90 text-sm"></div>
            </div>
        </div>

        {{-- RIGHT: Contact panel + Seller profile (title first, then price) --}}
        <aside class="lg:col-span-1">
            <div class="sticky top-6 grid gap-4">

                {{-- Contact panel: Title first, then Price --}}
                <div class="bg-white rounded-xl shadow p-4 border">
                    <div class="font-display text-xl sm:text-3xl leading-tight line-clamp-2 mb-2">{{ $listing->title }}
                    </div>
                    <div class="font-display text-xl sm:text-xl leading-tight line-clamp-2 mb-2">
                        €{{ number_format($listing->price, 0) }}</div>

                    @if ($phoneNice)
                        <button id="reveal-phone-btn" type="button" data-phone-display="{{ $phoneNice }}"
                            data-phone-href="{{ $phoneHref }}"
                            class="block w-full text-center px-4 py-2 rounded-lg bg-green-600 text-white font-semibold mb-2">
                            📞 Show phone number
                        </button>
                    @endif

                    @if ($displayEmail)
                        @if ($isAuth)
                            <button id="open-email-btn" type="button"
                                class="block w-full text-center px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold mb-2">
                                ✉️ Send message
                            </button>
                        @else
                            <button id="open-email-login" type="button"
                                class="block w-full text-center px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold mb-2">
                                ✉️ Send message
                            </button>
                        @endif
                    @endif

                    <button id="share-btn" type="button"
                        class="block w-full text-center px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 font-medium">
                        🔗 Share
                    </button>
                </div>

                {{-- Seller profile: larger avatar & text --}}
                <div class="bg-white rounded-xl shadow p-5 border">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('users.show', ['user' => $listing->user_id]) }}" class="shrink-0"
                            aria-label="User profile">
                            <img src="{{ $avatarUrl }}" class="h-20 w-20 rounded-full border object-cover"
                                alt="avatar">
                        </a>
                        <div>
                            <a href="{{ route('users.show', ['user' => $listing->user_id]) }}"
                                class="text-lg font-semibold hover:underline">
                                {{ $user->name }}
                            </a>
                            <div class="text-sm text-gray-600">Member since
                                {{ optional($user->created_at)->format('m/Y') }}</div>
                        </div>
                    </div>
                    <div class="mt-4 text-base text-gray-700">
                        Active listings: <span class="font-semibold">{{ $sellerActiveCount }}</span>
                    </div>
                </div>

            </div>
        </aside>
    </article>

    {{-- Phone modal --}}
    @if ($phoneNice)
        <div id="phone-modal" class="fixed inset-0 z-[60] hidden">
            <div id="phone-backdrop" class="absolute inset-0 bg-black/60"></div>
            <div class="relative z-10 mx-auto mt-28 w-[92%] max-w-sm bg-white rounded-xl shadow-lg p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold">Contact phone</h3>
                    <button id="phone-close" aria-label="Close"
                        class="bg-white/90 rounded-full w-8 h-8 shadow flex items-center justify-center">✕</button>
                </div>

                <div class="text-center text-2xl font-bold mb-4" id="phone-number">—</div>

                <a id="phone-call" href="#"
                    class="block w-full text-center px-4 py-2 rounded-lg bg-green-600 text-white font-semibold">
                    Call
                </a>
            </div>
        </div>
    @endif

    {{-- Email modal (auth only) --}}
    @if ($displayEmail && $isAuth)
        <div id="email-modal" class="fixed inset-0 z-[60] hidden">
            <div id="email-backdrop" class="absolute inset-0 bg-black/60"></div>
            <div class="relative z-10 mx-auto mt-20 w-[92%] max-w-lg bg-white rounded-xl shadow-lg p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold">Send message</h3>
                    <button id="email-close" aria-label="Close"
                        class="bg-white/90 rounded-full w-8 h-8 shadow flex items-center justify-center">✕</button>
                </div>

                <form method="POST" action="{{ route('listings.contact', ['listing' => $listing->id]) }}">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Name</label>
                            <input id="email-name" name="name" type="text" value="{{ $prefillName }}"
                                class="w-full border rounded-lg px-3 py-2" placeholder="Your name">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Email</label>
                            <input id="email-from" name="email" type="email" value="{{ $prefillEmail }}"
                                class="w-full border rounded-lg px-3 py-2" placeholder="name@example.com">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Message</label>
                            <textarea id="email-message" name="message" rows="6" class="w-full border rounded-lg px-3 py-2">{{ $prefillMsg }}</textarea>
                        </div>
                        <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg py-2">
                            Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Login-required modal (for guests) --}}
    @if ($displayEmail && !$isAuth)
        <div id="login-required-modal" class="fixed inset-0 z-[60] hidden">
            <div id="login-required-backdrop" class="absolute inset-0 bg-black/60"></div>
            <div class="relative z-10 mx-auto mt-28 w-[92%] max-w-sm bg-white rounded-xl shadow-lg p-6">
                <button id="login-required-close"
                    class="absolute top-2 right-2 bg-white/90 rounded-full w-8 h-8 shadow flex items-center justify-center"
                    aria-label="Close">✕</button>

                <h3 class="text-lg font-semibold mb-2">Sign in required</h3>
                <p class="text-sm text-gray-600 mb-4">
                    To message the seller, please sign in to your account.
                </p>

                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}?intended={{ urlencode(url()->current()) }}"
                        class="px-3 py-2 text-center rounded-lg bg-blue-600 text-white font-semibold">
                        Sign in
                    </a>
                    <a href="{{ route('register') }}?intended={{ urlencode(url()->current()) }}"
                        class="px-3 py-2 text-center rounded-lg bg-gray-100 hover:bg-gray-200 font-medium">
                        Register
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
