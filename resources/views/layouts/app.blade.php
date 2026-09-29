<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="bg-base flex justify-center">
       <div class="container">
           <x-header />

           {{ $slot }}
            @livewireScripts

           <div class="my-4 border-b border-muted/40 w-full"></div>
           <x-footer />
       </div>
       <script>
           document.addEventListener('alpine:init', () => {
               Alpine.store('audio', {
                   click() {
                       const clickSound = new Audio("{{ asset('audio/enter_key.mp3') }}");
                       clickSound.volume = 0.3;
                       clickSound.currentTime = 0;
                       clickSound.play().catch(error => {
                           console.error("Audio playback error:", error);
                       });
                   }
               });
           });
       </script>
    </body>
</html>
