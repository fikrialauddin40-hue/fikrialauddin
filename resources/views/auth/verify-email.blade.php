<x-guest-layout>
    @section('auth-title', 'Verifikasi Email')
    @section('auth-subtitle', 'Konfirmasi alamat email kamu')

    <div class="mb-4 text-sm text-gray-600">
        Terima kasih sudah mendaftar! Sebelum mulai, silakan verifikasi email kamu dengan mengklik link yang sudah kami kirim.
        Jika tidak menerima email, kami akan dengan senang hati mengirim ulang.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
            <i class="fas fa-check-circle mr-1"></i>Link verifikasi baru telah dikirim ke email kamu.
        </div>
    @endif

    <div class="mt-5 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-secondary text-sm px-5 py-2.5">
                <i class="fas fa-envelope mr-2"></i>Kirim Ulang Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                <i class="fas fa-sign-out-alt mr-1"></i>Keluar
            </button>
        </form>
    </div>
</x-guest-layout>