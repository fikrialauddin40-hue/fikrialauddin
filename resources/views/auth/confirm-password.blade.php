<x-guest-layout>
    @section('auth-title', 'Konfirmasi Password')
    @section('auth-subtitle', 'Ini adalah area aman. Harap konfirmasi password kamu.')

    <div class="mb-4 text-sm text-gray-600">
        Ini adalah area aman aplikasi. Harap konfirmasi password kamu sebelum melanjutkan.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="input-field @error('password') border-red-300 @enderror" placeholder="Masukkan password">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary w-full py-3.5">
            <i class="fas fa-check mr-2"></i>Konfirmasi
        </button>
    </form>
</x-guest-layout>