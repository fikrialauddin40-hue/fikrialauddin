<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Daily Couple Report')</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-mesh">
    <div id="bg-effects" class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="glow glow-1"></div>
        <div class="glow glow-2"></div>
        <div class="glow glow-3"></div>
        <div class="bg-blob bg-blob-1"></div>
        <div class="bg-blob bg-blob-2"></div>
        <div class="bg-blob bg-blob-3"></div>
    </div>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-6">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-romantic-500 to-rose-500 rounded-2xl flex items-center justify-center shadow-lg shadow-romantic-200">
                        <i class="fas fa-heart text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-romantic-600 to-rose-500 bg-clip-text text-transparent">Daily Couple</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-2 px-8 py-8 bg-white/80 backdrop-blur-xl shadow-xl shadow-romantic-100/50 overflow-hidden sm:rounded-2xl border border-romantic-100/50">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">@yield('auth-title', 'Selamat Datang')</h2>
                    <p class="text-gray-500 text-sm mt-1">@yield('auth-subtitle', 'Masuk ke akun Daily Couple kamu')</p>
                </div>
                {{ $slot }}
            </div>
            <p class="mt-6 text-xs text-gray-400">&copy; {{ date('Y') }} Daily Couple Report. Made with <i class="fas fa-heart text-romantic-400"></i></p>
        </div>
    </body>
</html>