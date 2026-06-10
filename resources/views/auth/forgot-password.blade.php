<x-guest-layout>
    @section('auth-title', 'Lupa Password')
    @section('auth-subtitle', 'Tenang, kami bantu reset password kamu')

    <div class="mb-4 text-sm text-gray-600">
        Lupa password? Masukkan email kamu dan kami akan kirim link reset password.
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input-field @error('email') border-red-300 @enderror" placeholder="Email kamu">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary w-full py-3.5">
            <i class="fas fa-paper-plane mr-2"></i>Kirim Link Reset
        </button>

        <p class="text-center text-sm text-gray-500">
            <a href="{{ route('login') }}" class="text-romantic-600 hover:text-romantic-700 font-medium">
                <i class="fas fa-arrow-left mr-1"></i>Kembali ke Login
            </a>
        </p>
    </form>
</x-guest-layout>