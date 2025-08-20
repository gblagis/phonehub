@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Post a new listing</h1>
    <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data"
        class="bg-white border rounded p-4 grid gap-3">
        @csrf
        @include('listings.form-fields')
        <button class="bg-green-600 text-white rounded p-2">Publish</button>
    </form>

    <script>
        const input = document.querySelector('input[name="photos[]"]');
        if (input) {
            input.addEventListener('change', e => {
                const c = document.getElementById('previews');
                c.innerHTML = '';
                [...e.target.files].slice(0, 5).forEach(f => {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(f);
                    img.className = 'h-24 w-24 object-cover rounded border mr-2 mb-2';
                    c.appendChild(img);
                });
            });
        }
    </script>
@endsection
