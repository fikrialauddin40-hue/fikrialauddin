<x-guest-layout>
    @section('auth-title', 'Masuk')
    @section('auth-subtitle', 'Selamat datang kembali, cinta!')

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="input-field @error('email') border-red-300 @enderror" placeholder="cth: kamu@couple.com">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="input-field @error('password') border-red-300 @enderror" placeholder="Masukkan password">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-romantic-600 focus:ring-romantic-300">
                <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-romantic-600 hover:text-romantic-700 font-medium">Lupa password?</a>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full py-3.5">
            <i class="fas fa-heart mr-2"></i>Masuk
        </button>

        <p class="text-center text-sm text-gray-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-romantic-600 hover:text-romantic-700 font-medium">Daftar</a>
        </p>
    </form>
</x-guest-layout>