<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Panadería') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased selection:bg-bakery-gold selection:text-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 gradient-bakery-bg relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-bakery-gold/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-bakery-peach/30 rounded-full blur-3xl"></div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-10 glass-card-white shadow-glow-strong overflow-hidden sm:rounded-3xl relative z-10 border border-white/50 animate-fade-in">
                <div class="flex flex-col items-center justify-center mb-8">
                    <a href="/" class="flex flex-col items-center hover-lift">
                        @if(isset($globalSettings['shop_logo']))
                            <div class="relative mb-3">
                                <div class="absolute inset-0 bg-bakery-gold/20 blur-xl rounded-full"></div>
                                <img src="{{ asset('storage/' . $globalSettings['shop_logo']) }}" alt="Logo" class="relative h-24 w-auto object-contain">
                            </div>
                        @endif
                        <div class="inline-flex items-center gap-2 bg-bakery-gold/10 backdrop-blur-sm px-4 py-1.5 rounded-full border border-bakery-gold/30">
                            <h1 class="text-lg font-black tracking-widest text-bakery-dark drop-shadow-sm">
                                {{ strtoupper($globalSettings['shop_name'] ?? 'PANADERÍA') }}
                            </h1>
                        </div>
                    </a>
                </div>

                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-sm text-bakery-dark/60 z-10 relative">
                &copy; {{ date('Y') }} {{ $globalSettings['shop_name'] ?? 'Panadería' }}. {{ __('All rights reserved.') }}
            </div>
        </div>
    </body>
</html>
