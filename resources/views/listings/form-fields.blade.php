@php($listing = $listing ?? null)

<div class="grid md:grid-cols-2 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium mb-1">Title</label>
        <input class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" name="title"
            value="{{ old('title', $listing->title ?? '') }}" placeholder="e.g. iPhone 13 Pro Max">
        @error('title')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Brand</label>
        <input class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" name="brand"
            value="{{ old('brand', $listing->brand ?? '') }}" placeholder="e.g. Apple">
        @error('brand')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Model</label>
        <input class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" name="model"
            value="{{ old('model', $listing->model ?? '') }}">
        @error('model')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Year</label>
        <input type="number" min="2007" max="{{ now()->year }}"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" name="year"
            value="{{ old('year', $listing->year ?? '') }}">
        @error('year')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Price (€)</label>
        <input type="number" step="0.01" min="0"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" name="price"
            value="{{ old('price', $listing->price ?? '') }}">
        @error('price')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">OS</label>
        <select class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" name="os">
            <option value="">Choose OS</option>
            @foreach (['iOS', 'Android'] as $os)
                <option value="{{ $os }}" @selected(old('os', $listing->os ?? '') === $os)>{{ $os }}</option>
            @endforeach
        </select>
        @error('os')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Condition</label>
        <select class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" name="condition">
            @foreach (['New', 'Like New', 'Good', 'Fair', 'Needs Repair'] as $c)
                <option value="{{ $c }}" @selected(old('condition', $listing->condition ?? '') === $c)>{{ $c }}</option>
            @endforeach
        </select>
        @error('condition')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Color</label>
        <input class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" name="color"
            value="{{ old('color', $listing->color ?? '') }}">
        @error('color')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">City</label>
        <input class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" name="city"
            value="{{ old('city', $listing->city ?? '') }}">
        @error('city')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-6">
    <label class="block text-sm font-medium mb-1">Description</label>
    <textarea class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" name="description"
        rows="5">{{ old('description', $listing->description ?? '') }}</textarea>
    @error('description')
        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="grid md:grid-cols-2 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium mb-1">Contact Phone</label>
        <input type="tel" inputmode="numeric" autocomplete="tel" placeholder="+30 698 123 4567"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" name="contact_phone"
            value="{{ old('contact_phone', $listing->contact_phone ?? (auth()->user()->phone ?? '')) }}" required>
        @error('contact_phone')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Contact Email</label>
        <input type="email" autocomplete="email"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" name="contact_email"
            value="{{ old('contact_email', $listing->contact_email ?? (auth()->user()->email ?? '')) }}">
        @error('contact_email')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Uploader νέων φωτογραφιών + previews (create & edit) --}}
<div class="mb-6">
    <label class="block text-sm font-medium mb-1">Photos</label>

    <div class="flex items-center gap-3">
        <input id="photos" name="photos[]" type="file" accept="image/*" multiple class="sr-only">
        <label for="photos"
            class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 cursor-pointer">
            Choose files
        </label>
        <span id="photos-status" class="text-sm text-gray-500">No files selected.</span>
    </div>

    <div id="previews" class="mt-3 flex flex-wrap gap-2"></div>
    @if ($errors->has('photos') || $errors->has('photos.*'))
        <div class="text-red-600 text-sm mt-2">
            {{ $errors->first('photos') ?: $errors->first('photos.*') }}
        </div>
    @endif
</div>
