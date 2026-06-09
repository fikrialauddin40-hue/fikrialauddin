<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Daily Couple Report</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-mesh">
    <div id="bg-effects" class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="glow glow-1"></div>
        <div class="glow glow-2"></div>
        <div class="glow glow-3"></div>
        <div class="bg-blob bg-blob-1"></div>
        <div class="bg-blob bg-blob-2"></div>
        <div class="bg-blob bg-blob-3"></div>
        <div class="bg-blob bg-blob-4"></div>
        <div class="bg-blob bg-blob-5"></div>
    </div>
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-gradient-to-b from-romantic-600 via-romantic-700 to-rose-800 shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto" id="sidebar">
            <div class="flex items-center justify-between h-16 px-6 border-b border-white/10">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-9 h-9 bg-white/20 rounded-lg">
                        <i class="fas fa-heart text-white text-lg"></i>
                    </div>
                    <span class="text-lg font-bold text-white tracking-wide">Daily Couple</span>
                </a>
                <button onclick="toggleSidebar()" class="lg:hidden text-white/60 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white shadow-lg' : 'text-romantic-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-chart-pie w-5 text-center mr-3 {{ request()->routeIs('dashboard') ? '' : 'text-romantic-300' }}"></i>
                    Dashboard
                </a>
                <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-white/20 text-white shadow-lg' : 'text-romantic-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-file-alt w-5 text-center mr-3 {{ request()->routeIs('reports.*') ? '' : 'text-romantic-300' }}"></i>
                    Laporan Harian
                </a>
                <a href="{{ route('couple.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('couple.*') ? 'bg-white/20 text-white shadow-lg' : 'text-romantic-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-link w-5 text-center mr-3 {{ request()->routeIs('couple.*') ? '' : 'text-romantic-300' }}"></i>
                    Hubungan Pasangan
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-white/20 text-white shadow-lg' : 'text-romantic-100 hover:bg-white/10 hover:text-white' }}">
                    <i class="fas fa-user w-5 text-center mr-3 {{ request()->routeIs('profile.*') ? '' : 'text-romantic-300' }}"></i>
                    Profil Saya
                </a>
            </nav>

            <div class="px-3 py-4 border-t border-white/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 text-sm font-medium text-romantic-200 rounded-xl hover:bg-white/10 hover:text-white transition-all duration-200">
                        <i class="fas fa-sign-out-alt w-5 text-center mr-3 text-romantic-300"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Overlay untuk mobile --}}
        <div class="fixed inset-0 z-20 bg-black/50 lg:hidden hidden" id="sidebar-overlay" onclick="toggleSidebar()"></div>

        {{-- Main Content --}}
        <div class="flex flex-col flex-1 w-0 min-h-screen">
            {{-- Top Bar --}}
            <header class="sticky top-0 z-10 bg-white/80 backdrop-blur-xl border-b border-gray-200 shadow-sm">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <div class="flex items-center">
                        <button onclick="toggleSidebar()" class="lg:hidden mr-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-500">
                            <i class="fas fa-heart text-romantic-500"></i>
                            <span>/</span>
                            <span class="text-gray-900 font-medium">@yield('title', 'Dashboard')</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-3">
                            <div class="hidden sm:block text-right">
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                @if(Auth::user()->hasPartner())
                                    <p class="text-xs text-romantic-500"><i class="fas fa-heart mr-1"></i>{{ Auth::user()->partner->name }}</p>
                                @else
                                    <p class="text-xs text-gray-500">Belum punya pasangan</p>
                                @endif
                            </div>
                            <div class="relative">
                                @if(Auth::user()->foto)
                                    <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="foto" class="w-9 h-9 rounded-full object-cover ring-2 ring-romantic-200">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-romantic-500 to-rose-600 flex items-center justify-center text-white text-sm font-bold ring-2 ring-romantic-200">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                {{-- Alert Success --}}
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center space-x-3 text-green-700 animate-fade-in">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                {{-- Alert Error --}}
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center space-x-3 text-red-700 animate-fade-in">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Alert Info --}}
                @if(session('info'))
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-center space-x-3 text-blue-700 animate-fade-in">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span class="text-sm font-medium">{{ session('info') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        @if(session('success') || session('error') || session('info'))
        setTimeout(() => {
            document.querySelectorAll('[class*="animate-fade-in"]').forEach(el => {
                el.style.transition = 'opacity 0.5s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 4000);
        @endif
    </script>
</body>
</html>