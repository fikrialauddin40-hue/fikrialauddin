<x-app-layout>
    @section('title', 'Detail Laporan')

    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <span class="text-gradient">Detail Laporan</span>
                </h1>
                <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('l, d F Y') }}</p>
            </div>
            @if($report->user_id === Auth::id())
                <div class="flex gap-2">
                    <a href="{{ route('reports.edit', $report) }}" class="btn-secondary text-sm px-4 py-2">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <form action="{{ route('reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Hapus laporan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-secondary text-sm px-4 py-2 text-red-500 border-red-200 hover:bg-red-50">
                            <i class="fas fa-trash mr-1"></i>
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Mood & Activity --}}
            <div class="card p-6">
                <div class="flex items-start space-x-4">
                    <span class="text-5xl">{{ $report->mood }}</span>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-900">{{ $report->activity }}</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Dilaporkan oleh <span class="font-medium text-romantic-600">{{ $report->user->name }}</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Times --}}
            @if($report->wake_up_time || $report->sleep_time)
            <div class="card p-6">
                <div class="grid grid-cols-2 gap-6">
                    @if($report->wake_up_time)
                    <div class="text-center">
                        <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-sun text-2xl text-amber-600"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-medium">Jam Bangun</p>
                        <p class="text-xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($report->wake_up_time)->format('H:i') }}</p>
                    </div>
                    @endif
                    @if($report->sleep_time)
                    <div class="text-center">
                        <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-moon text-2xl text-indigo-600"></i>
                        </div>
                        <p class="text-xs text-gray-500 font-medium">Jam Tidur</p>
                        <p class="text-xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($report->sleep_time)->format('H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Description --}}
            @if($report->description)
            <div class="card p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Cerita Harian</h3>
                <p class="text-gray-600 leading-relaxed">{{ $report->description }}</p>
            </div>
            @endif

            {{-- Gratitude --}}
            @if($report->gratitude)
            <div class="card p-6 bg-gradient-to-br from-romantic-50 to-rose-50 border-romantic-100">
                <h3 class="font-semibold text-gray-900 mb-3">
                    <i class="fas fa-heart text-romantic-500 mr-2"></i>Rasa Syukur
                </h3>
                <p class="text-gray-700 leading-relaxed">{{ $report->gratitude }}</p>
            </div>
            @endif

            {{-- Private Note --}}
            @if($report->private_note && $report->user_id === Auth::id())
            <div class="card p-6 border-dashed border-gray-300 bg-gray-50">
                <h3 class="font-semibold text-gray-600 mb-3">
                    <i class="fas fa-lock mr-2"></i>Catatan Pribadi
                </h3>
                <p class="text-gray-500 italic">{{ $report->private_note }}</p>
                <p class="text-xs text-gray-400 mt-2"><i class="fas fa-info-circle mr-1"></i>Hanya kamu yang bisa melihat ini</p>
            </div>
            @endif

            {{-- Photos --}}
            @if($report->documents->count() > 0)
            <div class="card p-6">
                <h3 class="font-semibold text-gray-900 mb-4">
                    <i class="fas fa-image text-romantic-500 mr-2"></i>Foto ({{ $report->documents->count() }})
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($report->documents as $doc)
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="block aspect-square rounded-xl overflow-hidden bg-gray-100 hover:opacity-90 transition-opacity">
                            <img src="{{ asset('storage/' . $doc->file_path) }}" alt="" class="w-full h-full object-cover">
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Meta --}}
            <div class="flex items-center justify-between text-xs text-gray-400">
                <span>Dibuat {{ $report->created_at->diffForHumans() }}</span>
                @if($report->created_at != $report->updated_at)
                    <span>Diedit {{ $report->updated_at->diffForHumans() }}</span>
                @endif
            </div>

            <div class="text-center">
                <a href="{{ route('reports.index') }}" class="text-sm text-romantic-600 hover:text-romantic-700 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali ke daftar laporan
                </a>
            </div>
        </div>
    </div>
</x-app-layout>