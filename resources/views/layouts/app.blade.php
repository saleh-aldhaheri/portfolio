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
    <body class="bg-base text-main min-h-screen flex flex-col">
        <x-header />
        <div class="container flex-1 mx-auto">
           {{ $slot }}
            @livewireScripts
        </div>
        <x-footer/>
        <script>
           document.addEventListener('alpine:init', () => {
               Alpine.store('audio', {
                   click() {
                       let  clickSound = new Audio("{{ asset('audio/enter_key.mp3') }}");
                       if(event.key !== 'enter'){
                           clickSound = new Audio("{{ asset('audio/press_key.mp3') }}");
                       }
                       clickSound.volume = 0.1;
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
