@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Search Results</h1>

    {{-- Μπάρα αναζήτησης  --}}
    <form class="mb-4 flex gap-2" method="GET" action="{{ route('listings.index') }}">
        <input name="q" value="{{ request('q') }}" class="border rounded p-2 w-full" placeholder="Search phones..." />
        <button class="bg-blue-600 text-white rounded px-4">Search</button>
    </form>

    {{-- ΦΙΛΤΡΑ: ΜΟΝΟ αν υπάρχει q --}}
    @if (filled(request('q')))
        <form class="grid grid-cols-2 md:grid-cols-7 gap-2 mb-4" method="GET" action="{{ route('listings.index') }}">
            {{-- κρατάμε το q ώστε τα φίλτρα να εφαρμόζονται πάνω στην αναζήτηση --}}
            <input type="hidden" name="q" value="{{ request('q') }}" />

            <input name="brand" value="{{ request('brand') }}" class="border rounded p-2" placeholder="Brand" />
            <input name="model" value="{{ request('model') }}" class="border rounded p-2" placeholder="Model" />
            <select name="os" class="border rounded p-2">
                <option value="">OS</option>
                <option @selected(request('os') === 'iOS')>iOS</option>
                <option @selected(request('os') === 'Android')>Android</option>
            </select>
            <select name="condition" class="border rounded p-2">
                <option value="">Condition</option>
                @foreach (['New', 'Like New', 'Good', 'Fair', 'Needs Repair'] as $c)
                    <option @selected(request('condition') === $c)>{{ $c }}</option>
                @endforeach
            </select>
            <input name="min_price" value="{{ request('min_price') }}" type="number" class="border rounded p-2"
                placeholder="Min €" />
            <input name="max_price" value="{{ request('max_price') }}" type="number" class="border rounded p-2"
                placeholder="Max €" />
            <button class="md:col-span-7 bg-blue-600 text-white rounded p-2">Apply filters</button>
        </form>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @forelse ($listings as $l)
            @include('listings.card', ['l' => $l])
        @empty
            <p>No listings match your filters.</p>
        @endforelse
    </div>

    {{-- διατήρηση query στα pagination links --}}
    <div class="mt-4">{{ $listings->appends(request()->query())->links() }}</div>
@endsection
