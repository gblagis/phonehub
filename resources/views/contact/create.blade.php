@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Contact us</h1>
    @if (session('success'))
        <div class="p-3 bg-green-100 border border-green-300 rounded mb-4">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('contact.store') }}" class="bg-white border rounded p-4 grid gap-3 max-w-lg">
        @csrf
        <input class="border rounded p-2" name="name" placeholder="Your name" required>
        <input class="border rounded p-2" name="email" type="email" placeholder="Your email" required>
        <textarea class="border rounded p-2" name="message" rows="6" placeholder="How can we help?" required></textarea>
        <button class="bg-blue-600 text-white rounded p-2">Send</button>
    </form>
@endsection
