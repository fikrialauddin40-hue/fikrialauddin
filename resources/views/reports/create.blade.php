<x-app-layout>
    @section('title', 'Laporan Baru')

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                <span class="text-gradient">Laporan Harian Baru</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">Ceritakan aktivitas dan perasaanmu hari ini</p>
        </div>

        <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="card p-6 space-y-6">
                {{-- Date --}}
                <div>
                    <label for="report_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="report_date" id="report_date" value="{{ old('report_date', date('Y-m-d')) }}" required class="input-field @error('report_date') border-red-300 @enderror">
                    @error('report_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mood --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Mood Hari Ini</label>
                    @error('mood')
                        <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                    @enderror
                    <div class="grid grid-cols-5 gap-3">
                        @php
                            $moods = [
                                '😊' => 'Bahagia',
                                '😍' => 'Jatuh Cinta',
                                '🥰' => 'Romantis',
                                '🤗' => 'Semangat',
                                '😴' => 'Lelah',
                                '😢' => 'Sedih',
                                '😡' => 'Kesal',
                                '🙏' => 'Bersyukur',
                                '😎' => 'Keren',
                                '🤔' => 'Bingung',
                            ];
                        @endphp
                        @foreach($moods as $emoji => $label)
                            <label class="flex flex-col items-center p-3 rounded-xl border-2 border-gray-100 cursor-pointer hover:border-romantic-300 hover:bg-romantic-50 transition-all has-[:checked]:border-romantic-500 has-[:checked]:bg-romantic-50 has-[:checked]:shadow-sm">
                                <input type="radio" name="mood" value="{{ $emoji }}" class="hidden" {{ old('mood') == $emoji ? 'checked' : '' }}>
                                <span class="text-3xl mb-1">{{ $emoji }}</span>
                                <span class="text-xs text-gray-500">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Times --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="wake_up_time" class="block text-sm font-medium text-gray-700 mb-1">Jam Bangun</label>
                        <input type="time" name="wake_up_time" id="wake_up_time" value="{{ old('wake_up_time') }}" class="input-field @error('wake_up_time') border-red-300 @enderror">
                        @error('wake_up_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="sleep_time" class="block text-sm font-medium text-gray-700 mb-1">Jam Tidur</label>
                        <input type="time" name="sleep_time" id="sleep_time" value="{{ old('sleep_time') }}" class="input-field @error('sleep_time') border-red-300 @enderror">
                        @error('sleep_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Activity --}}
                <div>
                    <label for="activity" class="block text-sm font-medium text-gray-700 mb-1">Aktivitas Hari Ini</label>
                    <input type="text" name="activity" id="activity" value="{{ old('activity') }}" required class="input-field @error('activity') border-red-300 @enderror" placeholder="Cth: Kerja, Kuliah, Jalan-jalan, dll">
                    @error('activity')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Cerita Harian</label>
                    <textarea name="description" id="description" rows="4" class="input-field @error('description') border-red-300 @enderror" placeholder="Ceritakan bagaimana harimu...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gratitude --}}
                <div>
                    <label for="gratitude" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-heart text-romantic-500 mr-1"></i>Rasa Syukur Hari Ini
                    </label>
                    <textarea name="gratitude" id="gratitude" rows="2" class="input-field @error('gratitude') border-red-300 @enderror" placeholder="Apa yang kamu syukuri hari ini?">{{ old('gratitude') }}</textarea>
                    @error('gratitude')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Private Note --}}
                <div>
                    <label for="private_note" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-lock text-gray-400 mr-1"></i>Catatan Pribadi
                    </label>
                    <textarea name="private_note" id="private_note" rows="2" class="input-field @error('private_note') border-red-300 @enderror" placeholder="Catatan khusus untuk dirimu sendiri (tidak terlihat pasangan)">{{ old('private_note') }}</textarea>
                    @error('private_note')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1"><i class="fas fa-info-circle mr-1"></i>Catatan ini hanya terlihat olehmu</p>
                </div>

                {{-- Photos --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Kegiatan</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-xl hover:border-romantic-300 transition-colors">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-3"></i>
                            <p class="text-sm text-gray-500">Seret foto ke sini atau <span class="text-romantic-600 font-medium">klik untuk upload</span></p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, WebP (max 5MB)</p>
                            <input type="file" name="photos[]" id="photos" multiple accept="image/*" class="hidden" onchange="updateFileLabel(this)">
                            <button type="button" onclick="document.getElementById('photos').click()" class="btn-secondary text-sm px-4 py-2 mt-3">
                                <i class="fas fa-camera mr-2"></i>Pilih Foto
                            </button>
                            <div id="file-list" class="mt-3 text-xs text-gray-500"></div>
                        </div>
                    </div>
                    @error('photos.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('reports.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Simpan Laporan
                </button>
            </div>
        </form>
    </div>

    <script>
        function updateFileLabel(input) {
            const list = document.getElementById('file-list');
            if (input.files.length > 0) {
                list.textContent = input.files.length + ' file dipilih';
            } else {
                list.textContent = '';
            }
        }
    </script>
</x-app-layout>