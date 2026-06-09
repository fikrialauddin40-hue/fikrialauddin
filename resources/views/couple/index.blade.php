<x-app-layout>
    @section('title', 'Hubungan Pasangan')

    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <span class="text-gradient">Hubungan Pasangan</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">Hubungkan akun kamu dengan pasangan untuk saling berbagi laporan harian.</p>
        </div>

        @if($user->hasPartner())
            {{-- Connected --}}
            <div class="card p-8 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-romantic-100 to-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-heart text-4xl text-romantic-500 animate-heartbeat"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Kamu Terhubung dengan Pasangan!</h2>
                <div class="mt-6 flex items-center justify-center space-x-6">
                    <div class="text-center">
                        @if($user->foto)
                            <img src="{{ asset('storage/' . $user->foto) }}" alt="" class="w-20 h-20 rounded-full object-cover ring-4 ring-romantic-200 mx-auto">
                        @else
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-romantic-500 to-rose-600 flex items-center justify-center text-white text-2xl font-bold ring-4 ring-romantic-200 mx-auto">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                        <p class="text-sm font-semibold text-gray-900 mt-2">{{ $user->name }}</p>
                    </div>
                    <div class="text-romantic-400">
                        <i class="fas fa-heart text-3xl"></i>
                    </div>
                    <div class="text-center">
                        @if($user->partner && $user->partner->foto)
                            <img src="{{ asset('storage/' . $user->partner->foto) }}" alt="" class="w-20 h-20 rounded-full object-cover ring-4 ring-rose-200 mx-auto">
                        @else
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-romantic-500 to-rose-600 flex items-center justify-center text-white text-2xl font-bold ring-4 ring-rose-200 mx-auto">
                                {{ $user->partner ? substr($user->partner->name, 0, 1) : '?' }}
                            </div>
                        @endif
                        <p class="text-sm font-semibold text-gray-900 mt-2">{{ $user->partner->name ?? 'Pasangan' }}</p>
                    </div>
                </div>
                <div class="mt-8">
                    <form action="{{ route('couple.disconnect') }}" method="POST" onsubmit="return confirm('Yakin ingin memutuskan hubungan dengan pasangan?')">
                        @csrf
                        <button type="submit" class="btn-secondary text-sm px-5 py-2.5 text-red-500 border-red-200 hover:bg-red-50 hover:border-red-300">
                            <i class="fas fa-unlink mr-2"></i>Putuskan Hubungan
                        </button>
                    </form>
                </div>
            </div>
        @else
            {{-- Not Connected --}}
            <div class="grid md:grid-cols-2 gap-6">
                {{-- Generate Code --}}
                <div class="card p-6">
                    <div class="w-12 h-12 bg-romantic-100 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-gift text-romantic-600 text-xl"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Buat Kode Undangan</h2>
                    <p class="text-sm text-gray-500 mt-2">Buat kode unik untuk diberikan ke pasanganmu. Mereka tinggal memasukkan kode ini untuk terhubung denganmu.</p>

                    @if($user->partner_code)
                        <div class="mt-6 p-4 bg-romantic-50 border border-romantic-200 rounded-xl">
                            <p class="text-xs text-romantic-600 font-medium mb-2">Kode Undangan Kamu</p>
                            <p class="text-3xl font-bold text-center text-romantic-600 tracking-widest">{{ $user->partner_code }}</p>
                            <p class="text-xs text-gray-500 text-center mt-2">Bagikan kode ini ke pasanganmu</p>
                        </div>
                        <div class="mt-4 flex gap-3">
                            <button onclick="copyCode()" class="btn-secondary text-sm px-4 py-2.5 flex-1">
                                <i class="fas fa-copy mr-2"></i>Salin Kode
                            </button>
                            <form action="{{ route('couple.regenerate') }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="btn-secondary text-sm px-4 py-2.5 w-full">
                                    <i class="fas fa-sync mr-2"></i>Buat Ulang
                                </button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('couple.generate') }}" method="POST" class="mt-6">
                            @csrf
                            <button type="submit" class="btn-primary w-full">
                                <i class="fas fa-magic mr-2"></i>Buat Kode Undangan
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Connect with Code --}}
                <div class="card p-6">
                    <div class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-key text-rose-600 text-xl"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Masukkan Kode Pasangan</h2>
                    <p class="text-sm text-gray-500 mt-2">Punya kode undangan dari pasangan? Masukkan di sini untuk terhubung.</p>

                    <form action="{{ route('couple.connect') }}" method="POST" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <input type="text" name="partner_code" required
                                class="input-field text-center text-2xl tracking-widest uppercase font-bold"
                                placeholder="XXXX-XXXX"
                                maxlength="8"
                                style="letter-spacing: 0.5em;"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 8)">
                        </div>
                        <button type="submit" class="btn-primary w-full">
                            <i class="fas fa-link mr-2"></i>Hubungkan
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Info --}}
        <div class="card p-6 bg-romantic-50/50">
            <div class="flex items-start space-x-3">
                <i class="fas fa-shield-alt text-romantic-500 mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm">Privasi Terjaga</h3>
                    <p class="text-xs text-gray-500 mt-1">Kamu hanya akan terhubung dengan satu pasangan. Hubungan bersifat mutual - kamu dan pasangan harus saling menyetujui. Data kalian aman dan privat.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyCode() {
            const code = '{{ $user->partner_code }}';
            navigator.clipboard.writeText(code).then(() => {
                alert('Kode berhasil disalin!');
            });
        }
    </script>
</x-app-layout>