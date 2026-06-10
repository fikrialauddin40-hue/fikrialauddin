<x-app-layout>
    @section('title', 'Dashboard')

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <span class="text-gradient">Dashboard</span>
                </h1>
                <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ $user->name }}!
                    @if($partner)
                        <span class="text-romantic-500">❤ {{ $partner->name }}</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('reports.create') }}" class="btn-primary text-sm px-5 py-2.5 w-full sm:w-auto text-center">
                <i class="fas fa-plus mr-2"></i>Laporan Hari Ini
            </a>
        </div>

        {{-- Couple Connection Alert --}}
        @if(!$user->hasPartner())
            <div class="card-gradient p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="w-12 h-12 bg-romantic-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-link text-romantic-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">Hubungkan dengan Pasangan</h3>
                        <p class="text-sm text-gray-500 mt-1">Buat kode undangan atau masukkan kode pasangan untuk mulai berbagi laporan harian.</p>
                    </div>
                    <a href="{{ route('couple.index') }}" class="btn-primary text-sm px-5 py-2.5 whitespace-nowrap">
                        <i class="fas fa-heart mr-2"></i>Hubungkan
                    </a>
                </div>
            </div>
        @endif

        {{-- Stats Overview --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card">
                <div class="relative">
                    <p class="text-sm text-gray-500 font-medium">Laporanku</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $myTotalReports }}</p>
                    <div class="mt-2 flex items-center text-xs text-romantic-500">
                        <i class="fas fa-fire mr-1"></i>Streak {{ $myStreak }} hari
                    </div>
                </div>
            </div>

            @if($partner)
            <div class="stat-card">
                <div class="relative">
                    <p class="text-sm text-gray-500 font-medium">Laporan {{ $partner->name }}</p>
                    <p class="text-2xl font-bold text-romantic-600 mt-1">{{ $partnerTotalReports }}</p>
                    <div class="mt-2 flex items-center text-xs text-romantic-500">
                        <i class="fas fa-fire mr-1"></i>Streak {{ $partnerStreak }} hari
                    </div>
                </div>
            </div>
            @else
            <div class="stat-card opacity-50">
                <div class="relative">
                    <p class="text-sm text-gray-500 font-medium">Laporan Pasangan</p>
                    <p class="text-2xl font-bold text-gray-300 mt-1">-</p>
                    <div class="mt-2 text-xs text-gray-400">Hubungkan pasangan dulu</div>
                </div>
            </div>
            @endif

            <div class="stat-card">
                <div class="relative">
                    <p class="text-sm text-gray-500 font-medium">Hari Bersama</p>
                    <p class="text-2xl font-bold text-gradient mt-1">
                        @if($user->anniversary_date)
                            {{ $daysTogether }}
                        @else
                            @if($partner)
                                {{ $daysTogether }}
                            @else
                                -
                            @endif
                        @endif
                    </p>
                    <div class="mt-2 text-xs text-gray-400">
                        @if($user->anniversary_date)
                            {{ \Carbon\Carbon::parse($user->anniversary_date)->translatedFormat('d M Y') }}
                        @else
                            Atur tanggal jadian
                        @endif
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="relative">
                    <p class="text-sm text-gray-500 font-medium">Total Platform</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalAllReports }}</p>
                    <div class="mt-2 text-xs text-gray-400">Semua laporan pengguna</div>
                </div>
            </div>
        </div>

        {{-- Today's Reports --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- My Today Report --}}
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-900">Laporanku Hari Ini</h2>
                    @if($myTodayReport)
                        <a href="{{ route('reports.edit', $myTodayReport) }}" class="text-sm text-romantic-600 hover:text-romantic-700 font-medium">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                    @else
                        <a href="{{ route('reports.create') }}" class="text-sm text-romantic-600 hover:text-romantic-700 font-medium">
                            <i class="fas fa-plus mr-1"></i>Isi
                        </a>
                    @endif
                </div>

                @if($myTodayReport)
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <span class="text-3xl">{{ $myTodayReport->mood }}</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $myTodayReport->activity }}</p>
                                <p class="text-xs text-gray-500">
                                    @if($myTodayReport->wake_up_time)
                                        Bangun {{ \Carbon\Carbon::parse($myTodayReport->wake_up_time)->format('H:i') }}
                                    @endif
                                    @if($myTodayReport->sleep_time)
                                        | Tidur {{ \Carbon\Carbon::parse($myTodayReport->sleep_time)->format('H:i') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($myTodayReport->description)
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $myTodayReport->description }}</p>
                        @endif
                        @if($myTodayReport->gratitude)
                            <div class="bg-romantic-50 rounded-xl p-3">
                                <p class="text-xs text-romantic-600 font-medium"><i class="fas fa-heart mr-1"></i>Rasa syukur</p>
                                <p class="text-sm text-gray-700 mt-1">{{ $myTodayReport->gratitude }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs text-gray-400">
                        <span><i class="far fa-clock mr-1"></i>{{ $myTodayReport->created_at->format('H:i') }}</span>
                        @if($myTodayReport->documents->count() > 0)
                            <span><i class="far fa-image mr-1"></i>{{ $myTodayReport->documents->count() }} foto</span>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-romantic-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-file text-2xl text-romantic-400"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Belum ada laporan hari ini</p>
                        <p class="text-xs text-gray-400 mt-1">Yuk isi laporan harianmu!</p>
                    </div>
                @endif
            </div>

            {{-- Partner Today Report --}}
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-900">
                        @if($partner)
                            Laporan {{ $partner->name }} Hari Ini
                        @else
                            Laporan Pasangan Hari Ini
                        @endif
                    </h2>
                </div>

                @if($partner && $partnerTodayReport)
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <span class="text-3xl">{{ $partnerTodayReport->mood }}</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $partnerTodayReport->activity }}</p>
                                <p class="text-xs text-gray-500">
                                    @if($partnerTodayReport->wake_up_time)
                                        Bangun {{ \Carbon\Carbon::parse($partnerTodayReport->wake_up_time)->format('H:i') }}
                                    @endif
                                    @if($partnerTodayReport->sleep_time)
                                        | Tidur {{ \Carbon\Carbon::parse($partnerTodayReport->sleep_time)->format('H:i') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($partnerTodayReport->description)
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $partnerTodayReport->description }}</p>
                        @endif
                        @if($partnerTodayReport->gratitude)
                            <div class="bg-romantic-50 rounded-xl p-3">
                                <p class="text-xs text-romantic-600 font-medium"><i class="fas fa-heart mr-1"></i>Rasa syukur {{ $partner->name }}</p>
                                <p class="text-sm text-gray-700 mt-1">{{ $partnerTodayReport->gratitude }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs text-gray-400">
                        <span><i class="far fa-clock mr-1"></i>{{ $partnerTodayReport->created_at->format('H:i') }}</span>
                        @if($partnerTodayReport->documents->count() > 0)
                            <span><i class="far fa-image mr-1"></i>{{ $partnerTodayReport->documents->count() }} foto</span>
                        @endif
                    </div>
                @elseif($partner && !$partnerTodayReport)
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-clock text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 text-sm">{{ $partner->name }} belum mengisi laporan</p>
                        <p class="text-xs text-gray-400 mt-1">Semoga segera diisi ya!</p>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-link text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Hubungkan pasangan dulu</p>
                        <a href="{{ route('couple.index') }}" class="text-sm text-romantic-600 hover:text-romantic-700 font-medium mt-2 inline-block">Hubungkan sekarang</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Weekly Activity & Recent Reports --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Weekly Activity Chart --}}
            <div class="card p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Aktivitas Minggu Ini</h2>
                <div class="flex items-end justify-between h-32 gap-2">
                    @foreach($weekDates as $i => $day)
                        <div class="flex flex-col items-center gap-2 flex-1">
                            <div class="flex gap-1 w-full justify-center">
                                <div class="w-3 h-{{ $myWeekData[$i] ? '8' : '3' }} bg-romantic-{{ $myWeekData[$i] ? '400' : '100' }} rounded-full transition-all duration-300" title="Kamu{{ $myWeekData[$i] ? '' : ' (kosong)' }}"></div>
                                @if($partner)
                                    <div class="w-3 h-{{ $partnerWeekData[$i] ? '8' : '3' }} bg-rose-{{ $partnerWeekData[$i] ? '400' : '100' }} rounded-full transition-all duration-300" title="{{ $partner->name }}{{ $partnerWeekData[$i] ? '' : ' (kosong)' }}"></div>
                                @endif
                            </div>
                            <span class="text-xs text-gray-400 font-medium">{{ $day }}</span>
                        </div>
                    @endforeach
                </div>
                @if($partner)
                <div class="flex items-center justify-center gap-6 mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-romantic-400 rounded-full"></div>
                        <span class="text-xs text-gray-500">Kamu</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-rose-400 rounded-full"></div>
                        <span class="text-xs text-gray-500">{{ $partner->name }}</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- My Recent Reports --}}
            <div class="card">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">Laporanku Terbaru</h2>
                    <a href="{{ route('reports.index') }}" class="text-sm text-romantic-600 hover:text-romantic-700 font-medium">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
                <div class="p-6">
                    @if($myReports->count() > 0)
                        <div class="space-y-3">
                            @foreach($myReports as $report)
                                <a href="{{ route('reports.show', $report) }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-romantic-50 transition-colors group">
                                    <span class="text-2xl">{{ $report->mood }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate group-hover:text-romantic-600">{{ $report->activity }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d M Y') }}</p>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-romantic-400"></i>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-sm">Belum ada laporan</p>
                            <a href="{{ route('reports.create') }}" class="text-sm text-romantic-600 hover:text-romantic-700 font-medium mt-2 inline-block">Buat laporan pertama</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Partner Recent Reports --}}
            <div class="card">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">
                        @if($partner) Laporan {{ $partner->name }} @else Laporan Pasangan @endif
                    </h2>
                </div>
                <div class="p-6">
                    @if($partner && $partnerRecentReports && $partnerRecentReports->count() > 0)
                        <div class="space-y-3">
                            @foreach($partnerRecentReports as $report)
                                <a href="{{ route('reports.show', $report) }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-romantic-50 transition-colors group">
                                    <span class="text-2xl">{{ $report->mood }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate group-hover:text-romantic-600">{{ $report->activity }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d M Y') }}</p>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-romantic-400"></i>
                                </a>
                            @endforeach
                        </div>
                    @elseif($partner && !$partnerRecentReports)
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-sm">{{ $partner->name }} belum punya laporan</p>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-link text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm">Hubungkan pasangan dulu</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Gratitude Wall --}}
        @if($myTodayReport?->gratitude || $partnerTodayReport?->gratitude)
        <div class="card-gradient p-6">
            <h2 class="font-semibold text-gray-900 mb-4"><i class="fas fa-heart text-romantic-500 mr-2"></i>Rasa Syukur Hari Ini</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                @if($myTodayReport?->gratitude)
                    <div class="bg-white/80 rounded-xl p-4">
                        <p class="text-xs text-romantic-500 font-medium mb-1">{{ $user->name }}</p>
                        <p class="text-sm text-gray-700">{{ $myTodayReport->gratitude }}</p>
                    </div>
                @endif
                @if($partnerTodayReport?->gratitude)
                    <div class="bg-white/80 rounded-xl p-4">
                        <p class="text-xs text-romantic-500 font-medium mb-1">{{ $partner->name }}</p>
                        <p class="text-sm text-gray-700">{{ $partnerTodayReport->gratitude }}</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Profile Quick Info --}}
        <div class="card p-6">
            <div class="flex items-center space-x-4">
                @if($user->foto)
                    <img src="{{ asset('storage/' . $user->foto) }}" alt="" class="w-16 h-16 rounded-full object-cover ring-4 ring-romantic-100">
                @else
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-romantic-500 to-rose-600 flex items-center justify-center text-white text-2xl font-bold ring-4 ring-romantic-100">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    @if($partner)
                        <p class="text-xs text-romantic-500 mt-1"><i class="fas fa-heart mr-1"></i>Bersama {{ $partner->name }}</p>
                    @endif
                </div>
                <a href="{{ route('profile.edit') }}" class="btn-secondary text-sm px-4 py-2">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
            </div>
        </div>
    </div>
</x-app-layout>