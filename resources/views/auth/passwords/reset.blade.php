@extends('layouts.app')

@section('content')
<div class="flex flex-col lg:flex-row min-h-screen">
    {{-- Bagian Kiri --}}
    <div class="w-full lg:w-1/2 bg-[#1E3A8A] flex items-center justify-center p-6 lg:p-12">
        {{-- Penambahan animasi: 'animate-fade-in' dengan durasi --}}
        <div class="w-full max-w-md text-center animate-fade-in" style="animation-duration: 1.5s;">
            <div class="mb-12">
                {{-- Penambahan animasi: 'animate-bounce' untuk logo --}}
                <img src="{{ asset('images/logo.png') }}" alt="Logo CBN Edusaku" class="h-32 mx-auto animate-bounce" onerror="this.style.display='none'; this.onerror=null;">
                {{-- Penambahan animasi: 'animate-fade-in-down' untuk teks judul dengan delay --}}
                <div class="mt-4 animate-fade-in-down" style="animation-delay: 0.5s;">
                    <h1 class="text-3xl lg:text-4xl font-light text-white leading-tight">Sistem Informasi Keuangan</h1>
                    <h2 class="text-2xl lg:text-3xl font-bold text-white tracking-widest mt-1">CBN Edusaku</h2>
                </div>
            </div>

            {{-- Penambahan animasi: 'animate-slide-up' dengan delay --}}
            <div class="bg-white p-8 rounded-xl shadow-2xl text-left animate-slide-up" style="animation-delay: 0.8s;">
                <h3 class="text-xl font-bold text-gray-800 mb-2">Atur Password Baru</h3>
                <p class="text-sm text-gray-500 mb-6">Masukkan password baru untuk akun {{ $identifier ?? '' }}</p>

                @if ($errors->any())
                    {{-- Penambahan animasi: 'animate-shake' untuk notifikasi error --}}
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 animate-shake" role="alert">
                        <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.perform_update', ['identifier' => $identifier]) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                        <div class="relative mt-1">
                            {{-- Penambahan transisi pada input field saat focus --}}
                            <input id="password" type="password" class="w-full pl-4 pr-10 py-3 bg-gray-100 text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out transform focus:scale-105" name="password" required autocomplete="new-password" placeholder="Masukkan Password Baru">
                            <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                                <svg class="h-5 w-5 icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg class="h-5 w-5 icon-eye-slash hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <div class="relative mt-1">
                            {{-- Penambahan transisi pada input field saat focus --}}
                            <input id="password_confirmation" type="password" class="w-full pl-4 pr-10 py-3 bg-gray-100 text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out transform focus:scale-105" name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi Password Baru">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                                <svg class="h-5 w-5 icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                 <svg class="h-5 w-5 icon-eye-slash hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        {{-- Penambahan efek hover yang lebih dinamis --}}
                        <button type="submit" class="w-full bg-[#FBBF24] text-[#1E3A8A] font-bold py-3 rounded-lg hover:bg-yellow-400 transition duration-300 ease-in-out shadow-lg transform hover:-translate-y-1 hover:shadow-xl">
                            Atur Password Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
   {{-- Bagian Kanan --}}
   <div class="hidden lg:flex w-1/2 relative items-center justify-center p-12">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/background.jpg') }}'); filter: blur(5px);"></div>
        
        {{-- Penambahan animasi: 'animate-fade-in' dengan delay agar muncul setelah form --}}
        <div class="relative z-10 w-full max-w-md animate-fade-in" style="animation-duration: 1.5s; animation-delay: 1.2s;">
            <div class="bg-[#1E3A8A] p-8 rounded-xl shadow-2xl text-white space-y-6 animate-slide-up">
                <h2 class="text-3xl font-bold text-yellow-300 mb-6">Informasi Pembayaran</h2>
                {{-- Penambahan transisi hover pada blok informasi --}}
                <div class="p-4 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 hover:bg-blue-900/50">
                    <h3 class="font-bold text-lg text-yellow-300">Metode Pembayaran</h3>
                    <p class="mt-1 text-gray-200">
                        Pembayaran dapat dilakukan melalui transfer bank ke Virtual Account yang telah disediakan.
                    </p>
                </div>
                {{-- Penambahan transisi hover pada blok informasi --}}
                <div class="p-4 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 hover:bg-blue-900/50">
                    <h3 class="font-bold text-lg text-yellow-300">Ketentuan Pembayaran</h3>
                    <ul class="list-disc list-inside mt-1 text-gray-200 space-y-1">
                        {{-- Penambahan transisi hover pada setiap list item --}}
                        <li class="transition duration-200 ease-in-out transform hover:translate-x-2">Pastikan melakukan pembayaran tepat waktu.</li>
                        <li class="transition duration-200 ease-in-out transform hover:translate-x-2">Status Pembayaran akan diperbarui dalam 1x24 Jam.</li>
                        <li class="transition duration-200 ease-in-out transform hover:translate-x-2">Konfirmasi Pembayaran akan dikirimkan melalui email.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- START: JavaScript for password toggle --}}
<script>
    function togglePasswordVisibility(inputId, button) {
        const passwordInput = document.getElementById(inputId);
        const iconEye = button.querySelector('.icon-eye');
        const iconEyeSlash = button.querySelector('.icon-eye-slash');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            iconEye.classList.add('hidden');
            iconEyeSlash.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            iconEye.classList.remove('hidden');
            iconEyeSlash.classList.add('hidden');
        }
    }
</script>
{{-- END: JavaScript for password toggle --}}
@endsection

{{-- START: Penambahan blok style untuk definisi animasi --}}
<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-fade-in {
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down {
        animation: fadeInDown 1s ease-in-out forwards;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .animate-slide-up {
        animation: slideUp 1s ease-in-out forwards;
    }

    @keyframes bounce {
	    0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
	    40% {transform: translateY(-20px);}
	    60% {transform: translateY(-10px);}
    }
    .animate-bounce {
        animation: bounce 1.5s ease;
    }
    
    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }
    .animate-shake {
        animation: shake 0.6s cubic-bezier(.36,.07,.19,.97) both;
    }
</style>
{{-- END: Penambahan blok style untuk definisi animasi --}}