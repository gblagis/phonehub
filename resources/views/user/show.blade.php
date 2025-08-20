@extends('layouts.app')

@section('content')
    @php($avatarPath = $user->avatar_path ?? ($user->avatar ?? null))
    @php($avatarUrl = $avatarPath ? Storage::url($avatarPath) : 'https://via.placeholder.com/120?text=U')

    <div class="max-w-5xl mx-auto grid gap-6">
        {{-- Κεφαλίδα προφίλ --}}
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <img src="{{ $avatarUrl }}" class="h-20 w-20 rounded-full border object-cover" alt="avatar">
            <div class="flex-1">
                <div class="text-xl font-semibold">{{ $user->name }}</div>
                <div class="text-sm text-gray-600">Member of {{ optional($user->created_at)->format('m/Y') }}</div>
                <div class="text-sm text-gray-700 mt-1">Active ads: <strong>{{ $activeCount }}</strong></div>
            </div>
        </div>

        {{-- Λίστα αγγελιών χρήστη --}}
        <div>
            <h2 class="text-lg font-semibold mb-3">User ads</h2>

            @if ($listings->isEmpty())
                <p class="text-gray-600">There are no active ads.</p>
            @else
                <div class="grid md:grid-cols-3 gap-4">
                    @foreach ($listings as $l)
                        <a href="{{ route('listings.show', $l) }}"
                            class="bg-white rounded-lg border hover:shadow transition">
                            <img class="h-40 w-full object-cover rounded-t-lg"
                                src="{{ $l->primaryImage ? Storage::url($l->primaryImage->path) : 'https://via.placeholder.com/600x400?text=No+Image' }}"
                                alt="{{ $l->title }}">
                            <div class="p-3">
                                <div class="font-semibold line-clamp-1">{{ $l->title }}</div>
                                <div class="text-sm text-gray-600">
                                    {{ trim($l->brand . ' • ' . $l->model, ' •') }}
                                </div>
                                <div class="text-lg font-bold mt-1">€{{ number_format($l->price, 0) }}</div>
                                @if ($l->city)
                                    <div class="text-xs text-gray-500 mt-1">📍 {{ $l->city }}</div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $listings->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
