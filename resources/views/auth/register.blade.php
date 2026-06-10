<x-guest-layout>
    @section('auth-title', 'Daftar')
    @section('auth-subtitle', 'Mulai ceritakan cintamu setiap hari')

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="input-field @error('name') border-red-300 @enderror" placeholder="Nama kamu">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="input-field @error('email') border-red-300 @enderror" placeholder="cth: kamu@couple.com">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="input-field @error('password') border-red-300 @enderror" placeholder="Minimal 8 karakter">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="input-field" placeholder="Ulangi password">
        </div>

        <button type="submit" class="btn-primary w-full py-3.5">
            <i class="fas fa-heart mr-2"></i>Daftar Gratis
        </button>

        <p class="text-center text-sm text-gray-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-romantic-600 hover:text-romantic-700 font-medium">Masuk</a>
        </p>
    </form>
</x-guest-layout>