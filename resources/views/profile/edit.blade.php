@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-xl font-semibold mb-6">Edit profile</h1>

        @if (session('success'))
            <div class="mb-4 text-green-600">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Avatar --}}
            <div class="mb-4">
                <label class="block text-sm font-medium">Profile photo</label>
                <div class="mt-2 flex items-center gap-4">
                    <img class="h-16 w-16 rounded-full object-cover border"
                        src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('images/default-avatar.png') }}"
                        alt="Avatar">
                    <input type="file" name="avatar" accept="image/*" class="text-sm">
                </div>
                @error('avatar')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Όνομα --}}
            <div class="mb-4">
                <label class="block text-sm font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                    class="mt-1 block w-full border px-4 py-2 rounded" required>
                @error('name')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                    class="mt-1 block w-full border px-4 py-2 rounded" required>
                @error('email')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Πόλη --}}
            <div class="mb-4">
                <label class="block text-sm font-medium">City</label>
                <input type="text" name="city" value="{{ old('city', Auth::user()->city) }}"
                    class="mt-1 block w-full border px-4 py-2 rounded">
                @error('city')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit"
                class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                Save
            </button>
        </form>
    </div>
    <hr class="my-8">

    <div class="mt-6">
        <h2 class="text-lg font-semibold text-red-600 mb-2">Delete Account</h2>

        <p class="text-sm text-gray-700 mb-4">
            Account deletion is permanent and cannot be undone. Enter your password to confirm.
        </p>

        @if ($errors->userDeletion->any())
            <div class="mb-4 text-red-600 text-sm">
                {{ $errors->userDeletion->first('password') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')

            <div class="mb-3">
                <input type="password" name="password" placeholder="Κωδικός πρόσβασης"
                    class="w-full border px-4 py-2 rounded" required>
            </div>

            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Delete Account
            </button>
        </form>
    </div>
@endsection
