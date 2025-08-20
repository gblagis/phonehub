@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit listing</h1>

    <form method="POST" action="{{ route('listings.update', ['listing' => $listing->id]) }}" enctype="multipart/form-data"
        class="bg-white border rounded p-4 grid gap-3">
        @csrf
        @method('PUT')
        @include('listings.form-fields', ['listing' => $listing])

        {{-- ΥΠΑΡΧΟΥΣΕΣ ΦΩΤΟΓΡΑΦΙΕΣ ΠΑΝΩ ΑΠΟ ΤΟ SAVE --}}
        @if ($listing->images->isNotEmpty())
            <div class="mt-2">
                <div class="font-semibold mb-2">Already uploaded photos</div>

                <div id="existing-images" class="flex gap-2 flex-wrap">
                    @foreach ($listing->images as $img)
                        {{-- Το label τυλίγει input+img ώστε το click να τσεκάρει το checkbox (χωρίς JS) --}}
                        <label class="relative group block" data-image-id="{{ $img->id }}">
                            {{-- Checkbox που στέλνει στο server τα IDs για διαγραφή (με a11y) --}}
                            <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="sr-only peer"
                                aria-label="Διαγραφή εικόνας #{{ $img->id }}">

                            {{-- Εικόνα: γκριζάρισμα όταν είναι τσεκαρισμένη + lazy loading --}}
                            <img src="{{ Storage::url($img->path) }}" alt="" loading="lazy"
                                class="h-24 w-24 object-cover rounded border transition
                                 peer-checked:opacity-40 peer-checked:ring-2 peer-checked:ring-red-400">

                            {{-- Ορατό “Χ” (tap/click λειτουργεί γιατί είναι μέσα στο <label>) --}}
                            <span
                                class="absolute -top-2 -right-2 z-10
                                   bg-red-600 text-white rounded-full w-6 h-6 text-xs
                                   flex items-center justify-center
                                   md:opacity-0 md:group-hover:opacity-100">
                                ×
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ΚΟΥΜΠΙ SAVE --}}
        <button class="bg-yellow-600 text-white rounded p-2">Save changes</button>
    </form>


@endsection
