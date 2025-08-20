@extends('layouts.app')

@section('content')
    {{-- Flash success --}}
    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Τίτλος & CTA --}}
    <h1 class="text-2xl font-bold mb-4">My Listings</h1>
    <div class="flex justify-end mb-3">
        <a href="{{ route('listings.create') }}" class="px-3 py-2 bg-green-600 text-white rounded">
            New Listing
        </a>
    </div>

    {{-- Cards λίστας --}}
    <div class="grid gap-3">
        @forelse($listings as $l)
            <div class="bg-white border rounded p-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img class="h-16 w-16 object-cover rounded border"
                        src="{{ $l->primaryImage ? Storage::url($l->primaryImage->path) : 'https://via.placeholder.com/64?text=No+img' }}"
                        alt="{{ $l->title }}" loading="lazy">
                    <div>
                        <a class="font-semibold hover:underline" href="{{ route('listings.show', $l) }}">
                            {{ $l->title }}
                        </a>
                        <div class="text-sm text-gray-600">
                            {{ $l->brand }} • {{ $l->model }} • €{{ number_format($l->price, 2) }}
                            @isset($l->status)
                                <span
                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs
                             {{ $l->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($l->status) }}
                                </span>
                            @endisset
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    @can('update', $l)
                        <a href="{{ route('listings.edit', $l) }}" class="px-3 py-1 bg-yellow-500 text-white rounded">
                            Edit
                        </a>
                    @endcan

                    @can('delete', $l)
                        {{-- Ανοίγει custom confirm modal --}}
                        <button type="button" class="px-3 py-1 bg-red-600 text-white rounded btn-open-delete"
                            data-url="{{ route('listings.destroy', $l) }}" data-title="{{ $l->title }}">
                            Delete
                        </button>
                    @endcan
                </div>
            </div>
        @empty
            <p>You haven’t posted any listings yet.</p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $listings->links() }}
    </div>

    {{-- Hidden DELETE form (γεμίζει δυναμικά) --}}
    <form id="del-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    {{-- Confirm Delete Modal --}}
    <div id="delete-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="del-title">
        <div id="delete-backdrop" class="absolute inset-0 bg-black/60"></div>

        <div class="relative z-10 mx-auto mt-28 w-[92%] max-w-md bg-white rounded-xl shadow-lg p-5">
            <div class="flex items-start justify-between">
                <h3 id="del-title" class="text-lg font-semibold">Delete listing</h3>
                <button id="del-close" class="bg-white/90 rounded-full w-8 h-8 shadow flex items-center justify-center"
                    aria-label="Close">✕</button>
            </div>

            <p class="mt-3 text-sm text-gray-700">
                Are you sure you want to delete <span id="del-name" class="font-semibold"></span>? This action cannot be
                undone.
            </p>

            <div class="mt-5 flex justify-end gap-2">
                <button id="del-cancel" type="button" class="px-3 py-2 rounded border">Cancel</button>
                <button id="del-confirm" type="button" class="px-3 py-2 rounded bg-red-600 text-white">Delete</button>
            </div>
        </div>
    </div>
@endsection
