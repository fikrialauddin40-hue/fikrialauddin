<x-app-layout>
    @section('title', 'Profil Saya')

    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <span class="text-gradient">Profil Saya</span>
            </h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi profil kamu</p>
        </div>

        {{-- Profile Info --}}
        <div class="card p-6">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="flex flex-col sm:flex-row items-start gap-6">
                    <div class="text-center">
                        <div class="relative inline-block">
                            @if(Auth::user()->foto)
                                <img src="{{ asset('storage/' . Auth::user()->foto) }}" id="preview" class="w-24 h-24 rounded-full object-cover ring-4 ring-romantic-200">
                            @else
                                <div id="preview" class="w-24 h-24 rounded-full bg-gradient-to-br from-romantic-500 to-rose-600 flex items-center justify-center text-white text-3xl font-bold ring-4 ring-romantic-200">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            <label for="foto" class="absolute bottom-0 right-0 w-8 h-8 bg-romantic-500 text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-romantic-600 transition-colors shadow-lg">
                                <i class="fas fa-camera text-xs"></i>
                            </label>
                            <input type="file" name="foto" id="foto" accept="image/*" class="hidden" onchange="previewImage(this)">
                        </div>
                    </div>

                    <div class="flex-1 space-y-4 w-full">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" required class="input-field @error('name') border-red-300 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}" required class="input-field @error('email') border-red-300 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select name="gender" id="gender" class="input-field">
                            <option value="">Pilih</option>
                            <option value="male" {{ Auth::user()->gender == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ Auth::user()->gender == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', Auth::user()->no_hp) }}" class="input-field">
                    </div>
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', Auth::user()->birth_date ? Auth::user()->birth_date->format('Y-m-d') : '') }}" class="input-field">
                    </div>
                    <div>
                        <label for="anniversary_date" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-heart text-romantic-500 mr-1"></i>Tanggal Jadian
                        </label>
                        <input type="date" name="anniversary_date" id="anniversary_date" value="{{ old('anniversary_date', Auth::user()->anniversary_date ? Auth::user()->anniversary_date->format('Y-m-d') : '') }}" class="input-field">
                    </div>
                </div>

                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea name="bio" id="bio" rows="3" class="input-field" placeholder="Ceritakan sedikit tentang dirimu...">{{ old('bio', Auth::user()->bio) }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        {{-- Password --}}
        <div class="card p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Ubah Password</h2>
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                    <input type="password" name="current_password" id="current_password" required class="input-field">
                    @error('current_password', 'updatePassword')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password" id="password" required class="input-field">
                    @error('password', 'updatePassword')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="input-field">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-key mr-2"></i>Ubah Password
                    </button>
                </div>
            </form>
        </div>

        {{-- Delete Account --}}
        <div class="card p-6 border-red-100">
            <h2 class="font-semibold text-red-600 mb-2">Hapus Akun</h2>
            <p class="text-sm text-gray-500 mb-4">Setelah akun dihapus, data tidak bisa dikembalikan.</p>
            <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Yakin ingin menghapus akun? Semua data akan hilang!')">
                @csrf
                @method('DELETE')
                <div class="flex items-center space-x-3">
                    <input type="password" name="password" placeholder="Masukkan password" required class="input-field flex-1">
                    <button type="submit" class="px-5 py-3 bg-red-500 text-white font-semibold rounded-xl hover:bg-red-600 transition-all duration-200">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.id = 'preview';
                        img.className = 'w-24 h-24 rounded-full object-cover ring-4 ring-romantic-200';
                        img.src = e.target.result;
                        preview.parentNode.replaceChild(img, preview);
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>