<x-app-layout>
    @section('title', 'Laporan Harian')

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <span class="text-gradient">Laporan Harian</span>
                </h1>
                <p class="text-sm text-gray-500 mt-1">Semua laporan harian kamu dan pasangan</p>
            </div>
            <a href="{{ route('reports.create') }}" class="btn-primary text-sm px-5 py-2.5 w-full sm:w-auto text-center">
                <i class="fas fa-plus mr-2"></i>Laporan Baru
            </a>
        </div>

        {{-- Filter --}}
        <div class="card p-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs text-gray-500 font-medium mb-1">Mood</label>
                    <select name="mood" class="input-field text-sm">
                        <option value="">Semua Mood</option>
                        <option value="😊" @selected(request('mood') == '😊')>😊 Bahagia</option>
                        <option value="😍" @selected(request('mood') == '😍')>😍 Jatuh Cinta</option>
                        <option value="😴" @selected(request('mood') == '😴')>😴 Lelah</option>
                        <option value="😢" @selected(request('mood') == '😢')>😢 Sedih</option>
                        <option value="😡" @selected(request('mood') == '😡')>😡 Kesal</option>
                        <option value="🤗" @selected(request('mood') == '🤗')>🤗 Semangat</option>
                        <option value="🥰" @selected(request('mood') == '🥰')>🥰 Romantis</option>
                        <option value="😎" @selected(request('mood') == '😎')>😎 Keren</option>
                        <option value="🙏" @selected(request('mood') == '🙏')>🙏 Bersyukur</option>
                        <option value="🤔" @selected(request('mood') == '🤔')>🤔 Bingung</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs text-gray-500 font-medium mb-1">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}" class="input-field text-sm">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs text-gray-500 font-medium mb-1">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}" class="input-field text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary text-sm px-4 py-2.5">
                        <i class="fas fa-search mr-1"></i>Filter
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn-secondary text-sm px-4 py-2.5">
                        <i class="fas fa-times mr-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Partner Reports --}}
        @if($partnerReports->count() > 0)
        <div class="card p-6">
            <h2 class="font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-heart text-romantic-500 mr-2"></i>
                Laporan Terbaru Pasangan
            </h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($partnerReports as $report)
                    <a href="{{ route('reports.show', $report) }}" class="block p-4 rounded-xl bg-romantic-50/50 hover:bg-romantic-100/50 transition-colors border border-romantic-100">
                        <div class="flex items-center space-x-3">
                            <span class="text-2xl">{{ $report->mood }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $report->activity }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d M Y') }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- My Reports --}}
        <div class="card">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Laporanku</h2>
            </div>
            <div class="p-6">
                @if($reports->count() > 0)
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($reports as $report)
                            <a href="{{ route('reports.show', $report) }}" class="block p-5 rounded-xl border border-gray-100 hover:border-romantic-200 hover:shadow-md transition-all duration-200 group">
                                <div class="flex items-start justify-between mb-3">
                                    <span class="text-3xl">{{ $report->mood }}</span>
                                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($report->report_date)->format('d M') }}</span>
                                </div>
                                <h3 class="font-semibold text-gray-900 text-sm group-hover:text-romantic-600">{{ $report->activity }}</h3>
                                @if($report->description)
                                    <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $report->description }}</p>
                                @endif
                                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-50">
                                    <div class="flex items-center space-x-2 text-xs text-gray-400">
                                        @if($report->wake_up_time)
                                            <span><i class="far fa-sun mr-1"></i>{{ \Carbon\Carbon::parse($report->wake_up_time)->format('H:i') }}</span>
                                        @endif
                                        @if($report->sleep_time)
                                            <span><i class="far fa-moon mr-1"></i>{{ \Carbon\Carbon::parse($report->sleep_time)->format('H:i') }}</span>
                                        @endif
                                    </div>
                                    @if($report->documents->count() > 0)
                                        <span class="text-xs text-romantic-400"><i class="far fa-image mr-1"></i>{{ $report->documents->count() }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $reports->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-romantic-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-alt text-3xl text-romantic-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Belum ada laporan</p>
                        <p class="text-sm text-gray-400 mt-1">Mulai catat aktivitas harianmu bersama pasangan.</p>
                        <a href="{{ route('reports.create') }}" class="btn-primary text-sm px-5 py-2.5 mt-4 inline-flex">
                            <i class="fas fa-plus mr-2"></i>Buat Laporan Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>