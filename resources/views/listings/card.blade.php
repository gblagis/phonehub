<a href="{{ route('listings.show',$l) }}" class="block bg-white rounded border hover:shadow">
  <img class="w-full h-40 object-cover rounded-t"
       src="{{ $l->primaryImage? asset('storage/'.$l->primaryImage->path) : 'https://via.placeholder.com/600x400?text=Phone' }}"
       alt="">
  <div class="p-3">
    <div class="font-semibold truncate">{{ $l->title }}</div>
    <div class="text-sm text-gray-600">{{ $l->brand }} • {{ $l->model }} • {{ $l->city }}</div>
    <div class="mt-1 font-bold">{{ number_format($l->price,2) }} €</div>
  </div>
</a>
