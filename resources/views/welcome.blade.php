<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daily Couple Report - Ceritakan Cintamu Setiap Hari</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white">
    {{-- Navbar --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-100/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-romantic-500 to-rose-500 rounded-xl flex items-center justify-center shadow-lg shadow-romantic-200">
                        <i class="fas fa-heart text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-romantic-600 to-rose-500 bg-clip-text text-transparent">Daily Couple</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary text-sm px-5 py-2.5">
                            <i class="fas fa-chart-pie mr-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-romantic-600 font-medium transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary text-sm px-5 py-2.5">
                            <i class="fas fa-heart mr-2"></i>Daftar Gratis
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="relative min-h-screen flex items-center overflow-hidden pt-20">
        <div class="absolute inset-0 bg-gradient-to-br from-romantic-50 via-white to-rose-50"></div>
        <div class="absolute top-0 -left-40 w-96 h-96 bg-romantic-200/30 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-40 w-96 h-96 bg-rose-200/30 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 right-1/4 w-64 h-64 bg-romantic-100/20 rounded-full blur-2xl animate-float"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8 animate-fade-in-up">
                    <div class="inline-flex items-center space-x-2 bg-romantic-100/50 text-romantic-700 px-4 py-2 rounded-full text-sm font-medium">
                        <i class="fas fa-heart text-romantic-500 text-xs"></i>
                        <span>#1 Pasangan Platform</span>
                        <i class="fas fa-heart text-romantic-500 text-xs"></i>
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight">
                        <span class="text-gray-900">Ceritakan</span>
                        <br>
                        <span class="text-gradient">Cintamu</span>
                        <br>
                        <span class="text-gray-900">Setiap Hari</span>
                    </h1>
                    <p class="text-lg text-gray-600 leading-relaxed max-w-xl">
                        Tempat spesial untuk kamu dan pasangan berbagi cerita, mood, dan aktivitas harian. 
                        Bangun hubungan yang lebih transparan dan romantis setiap harinya.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-primary text-base px-8 py-4">
                                <i class="fas fa-arrow-right mr-2"></i>Mulai Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-primary text-base px-8 py-4">
                                <i class="fas fa-heart mr-2"></i>Mulai Gratis
                            </a>
                            <a href="{{ route('login') }}" class="btn-secondary text-base px-8 py-4">
                                <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                            </a>
                        @endauth
                    </div>
                    <div class="flex items-center space-x-8 text-sm text-gray-500">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Gratis</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Private</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Romantis</span>
                        </div>
                    </div>
                </div>
                <div class="hidden lg:flex items-center justify-center animate-fade-in">
                    <div class="relative">
                        <div class="w-80 h-80 bg-gradient-to-br from-romantic-200 to-rose-200 rounded-full animate-float"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="w-40 h-40 bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-heart text-6xl text-romantic-500 animate-heartbeat"></i>
                                </div>
                                <p class="text-romantic-700 font-semibold">Share the love</p>
                            </div>
                        </div>
                        <div class="absolute -top-4 -right-4 bg-white rounded-2xl shadow-lg px-5 py-3 animate-float" style="animation-delay: 0.5s">
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl">😊</span>
                                <div>
                                    <p class="text-xs text-gray-500">Mood Hari Ini</p>
                                    <p class="text-sm font-semibold text-gray-800">Bahagia</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl shadow-lg px-5 py-3 animate-float" style="animation-delay: 1s">
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl">🔥</span>
                                <div>
                                    <p class="text-xs text-gray-500">Streak</p>
                                    <p class="text-sm font-semibold text-gray-800">7 Hari</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="py-16 bg-white border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <p class="text-3xl lg:text-4xl font-bold text-gradient">{{ number_format($totalReports ?? 1250) }}+</p>
                    <p class="text-gray-500 text-sm mt-2">Total Laporan</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl lg:text-4xl font-bold text-gradient">{{ $totalCouples ?? 500 }}+</p>
                    <p class="text-gray-500 text-sm mt-2">Pasangan Aktif</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl lg:text-4xl font-bold text-gradient">{{ $totalDays ?? 15000 }}+</p>
                    <p class="text-gray-500 text-sm mt-2">Hari Kebersamaan</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl lg:text-4xl font-bold text-gradient">{{ $happyMoods ?? 95 }}%</p>
                    <p class="text-gray-500 text-sm mt-2">Mood Bahagia</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-20 lg:py-28 bg-gradient-to-b from-white to-romantic-50/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-romantic-500 font-semibold text-sm uppercase tracking-wider">Fitur</span>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-3">Semua yang Kamu Butuhkan</h2>
                <p class="text-gray-600 mt-4">Dibuat khusus untuk membantu hubunganmu semakin romantis dan transparan</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="card p-8 group hover:-translate-y-1">
                    <div class="w-14 h-14 bg-gradient-to-br from-romantic-100 to-romantic-200 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-face-smile text-2xl text-romantic-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Mood Tracker</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Catat mood harianmu dengan emoji. Lihat tren perasaan kalian berdua dari waktu ke waktu.</p>
                </div>
                <div class="card p-8 group hover:-translate-y-1">
                    <div class="w-14 h-14 bg-gradient-to-br from-rose-100 to-rose-200 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-calendar-day text-2xl text-rose-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Daily Report</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Bagikan aktivitas harianmu, jam bangun, jam tidur, dan momen spesial setiap hari.</p>
                </div>
                <div class="card p-8 group hover:-translate-y-1">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-100 to-amber-200 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-link text-2xl text-amber-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Couple Connection</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Hubungkan akun dengan pasangan menggunakan kode undangan unik.</p>
                </div>
                <div class="card p-8 group hover:-translate-y-1">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-fire text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Streak Tracker</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Jaga konsistensi laporan harian dengan streak. Tantang pasangan untuk tidak putus!</p>
                </div>
                <div class="card p-8 group hover:-translate-y-1">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-image text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Foto & Momen</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Unggah foto kegiatan sehari-hari. Simpan kenangan indah bersama pasangan.</p>
                </div>
                <div class="card p-8 group hover:-translate-y-1">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-100 to-cyan-200 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-line text-2xl text-cyan-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Dashboard Lengkap</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Lihat ringkasan aktivitas, statistik, dan countdown hari jadian di satu tempat.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-romantic-500 font-semibold text-sm uppercase tracking-wider">Testimoni</span>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-3">Apa Kata Mereka?</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="card-gradient p-8">
                    <div class="flex items-center space-x-1 text-romantic-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">"Aplikasi ini bikin hubungan kami makin dekat. Setiap hari jadi punya cerita yang bisa dibagi berdua. Love it!"</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-romantic-300 to-rose-300 rounded-full flex items-center justify-center text-white font-bold">A</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Alex & Alicia</p>
                            <p class="text-xs text-gray-500">3 bulan bersama</p>
                        </div>
                    </div>
                </div>
                <div class="card-gradient p-8">
                    <div class="flex items-center space-x-1 text-romantic-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">"Fitur streak bikin kita semangat buat laporan tiap hari. Seru banget lihat statistik mood pasangan!"</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-romantic-300 to-rose-300 rounded-full flex items-center justify-center text-white font-bold">R</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Raka & Dinda</p>
                            <p class="text-xs text-gray-500">1 tahun bersama</p>
                        </div>
                    </div>
                </div>
                <div class="card-gradient p-8">
                    <div class="flex items-center space-x-1 text-romantic-400 mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">"Transparansi dalam hubungan jadi lebih mudah. Kami bisa saling tahu aktivitas tanpa harus nanya terus."</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-romantic-300 to-rose-300 rounded-full flex items-center justify-center text-white font-bold">B</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Bima & Sari</p>
                            <p class="text-xs text-gray-500">6 bulan bersama</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-20 lg:py-28 bg-gradient-to-br from-romantic-500 via-romantic-600 to-rose-600 relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-10 left-10 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-3xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">Siap Ceritakan Cintamu?</h2>
            <p class="text-romantic-100 text-lg mb-10">Gabung sekarang dan mulai bagikan cerita cinta kalian setiap hari!</p>
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-8 py-4 bg-white text-romantic-600 font-bold rounded-xl hover:bg-romantic-50 transition-all duration-200 shadow-xl">
                    <i class="fas fa-arrow-right mr-2"></i>Ke Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-white text-romantic-600 font-bold rounded-xl hover:bg-romantic-50 transition-all duration-200 shadow-xl">
                    <i class="fas fa-heart mr-2"></i>Daftar Gratis Sekarang
                </a>
            @endauth
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <div class="w-8 h-8 bg-gradient-to-br from-romantic-400 to-rose-400 rounded-lg flex items-center justify-center">
                        <i class="fas fa-heart text-white text-sm"></i>
                    </div>
                    <span class="text-white font-semibold">Daily Couple Report</span>
                </div>
                <div class="flex items-center space-x-6 text-sm">
                    <span>&copy; {{ date('Y') }} Daily Couple Report. Made with <i class="fas fa-heart text-romantic-400"></i></span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>