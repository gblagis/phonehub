@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6 bg-white rounded shadow relative">

        {{-- Edit Profile Button --}}
        <a href="{{ route('profile.edit') }}"
            class="absolute top-6 right-6 inline-flex items-center px-4 py-2 border border-gray-300 rounded hover:bg-gray-100 text-sm font-medium">
            ✏ Edit profile
        </a>

        {{-- Πληροφορίες χρήστη --}}
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 mb-8">
            {{-- Avatar --}}
            <img class="w-32 h-32 rounded-full object-cover border"
                src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('images/default-avatar.png') }}"
                alt="{{ Auth::user()->name }}">

            {{-- Info --}}
            <div class="flex-1 space-y-2">
                <h1 class="text-2xl font-semibold">{{ Auth::user()->name }}</h1>


                <div class="mt-4 space-y-1 text-sm text-gray-700">
                    <div>
                        📍 City: {{ Auth::user()->city ?? 'Not defined' }}
                    </div>
                    <div>
                        📧 Email: {{ Auth::user()->email }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Αγγελίες χρήστη --}}
        <div>
            <h2 class="text-lg font-semibold mb-4">My ads</h2>

            @if ($listings->isEmpty())
                <p class="text-gray-500">You haven't posted any ads yet.</p>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($listings as $l)
                        @include('listings.card', ['l' => $l])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
