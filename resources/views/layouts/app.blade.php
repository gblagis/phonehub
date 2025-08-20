<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="{{ asset('images/logo-icon.svg') }}?v=2" type="image/svg+xml">
  <title>{{ $title ?? 'PhoneHub' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">
<style>
  /* Χρησιμοποιούμε τη νέα γραμματοσειρά μόνο όπου τη ζητάμε */
  .font-display {
    font-family: "Outfit", ui-sans-serif, system-ui, -apple-system, "Segoe UI",
                 Roboto, "Helvetica Neue", Arial, "Noto Sans",
                 "Apple Color Emoji","Segoe UI Emoji";
  }
</style>

  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">

  {{-- Navigation --}}
  @include('layouts.navigation')

  <main class="max-w-7xl mx-auto p-4">@yield('content')</main>

  <script>
    (function(){
      const header = document.querySelector('header');
      const mob = document.getElementById('mobileMenu');
      const btn = document.getElementById('navToggle');

      function onScroll(){
        if (window.scrollY > 4) header.classList.add('shadow-md');
        else header.classList.remove('shadow-md');
      }
      onScroll(); window.addEventListener('scroll', onScroll);

      if (btn && mob) btn.addEventListener('click', ()=> mob.classList.toggle('hidden'));
    })();
  </script>
</body>
</html>
