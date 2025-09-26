<header class="sticky top-0 z-50">
  <div class="bg-white/80 backdrop-blur border-b">
    <div class="max-w-7xl mx-auto px-4">
      <div class="h-16 flex items-center justify-between gap-3">

        {{-- LEFT: Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0 group">
          <img src="{{ asset('images/logo.svg') }}" alt="PhoneHub logo" class="h-10 w-auto">
        </a>

        {{-- CENTER: Search --}}
        <form action="{{ route('listings.index') }}" class="hidden md:flex items-center gap-2 flex-1 max-w-xl mx-6">
          <div class="relative flex-1">
            <input name="q" value="{{ request('q') }}"
                   class="w-full rounded-lg border px-4 pl-10 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Search phones..."/>
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle cx="11" cy="11" r="8" stroke-width="2"></circle>
              <path d="M21 21l-3.5-3.5" stroke-width="2"></path>
            </svg>
          </div>
          <button class="px-4 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
            Search
          </button>
        </form>

        {{-- RIGHT: Links + Avatar Dropdown --}}
        <nav class="hidden md:flex items-center gap-4">
          <a href="{{ route('contact.create') }}"
             class="text-sm hover:text-indigo-700 {{ request()->routeIs('contact.*') ? 'text-indigo-700 font-semibold' : 'text-slate-700' }}">
             Contact Us
          </a>

          @auth
            <!-- AVATAR DROPDOWN -->
            <div x-data="{ open: false }" class="relative">
              <button @click="open = !open" class="focus:outline-none">
                <img
                    class="h-8 w-8 rounded-full object-cover"
                    src="{{ Auth::user()->avatar
                        ? asset('storage/' . Auth::user()->avatar)
                        : asset('images/default-avatar.png') }}"
                    alt="{{ Auth::user()->name }}"
                >
              </button>

              <div
                  x-show="open"
                  @click.away="open = false"
                  x-cloak
                  class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg z-50 py-1"
              >
                  <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Profile</a>
                  <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">My Listings</a>
                  <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100">Logout</button>
                  </form>
              </div>
            </div>

            <a href="{{ route('listings.create') }}"
               class="inline-flex items-center px-3 py-2 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm font-semibold hover:opacity-90 transition">
               Post
            </a>
          @else
            <a href="{{ route('login') }}"
               class="text-sm hover:text-indigo-700 {{ request()->routeIs('login') ? 'text-indigo-700 font-semibold' : 'text-slate-700' }}">
               Login
            </a>
            <a href="{{ route('register') }}"
               class="text-sm hover:text-indigo-700 {{ request()->routeIs('register') ? 'text-indigo-700 font-semibold' : 'text-slate-700' }}">
               Register
            </a>
          @endauth
        </nav>
      </div>
    </div>
  </div>
</header>
