@extends('layouts.app')

@section('content')
<div class="flex flex-col lg:flex-row min-h-screen">
    {{-- Bagian Kiri --}}
    <div class="w-full lg:w-1/2 bg-[#1E3A8A] flex items-center justify-center p-6 lg:p-12">
        {{-- Penambahan animasi: 'animate-fade-in' dengan durasi lebih lambat --}}
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
                <h3 class="text-xl font-bold text-gray-800 mb-2">Lupa Password</h3>
                <p class="text-sm text-gray-500 mb-6">Masukkan NIM atau Username Anda untuk memulai proses reset password</p>

                @if (session('status'))
                    {{-- Penambahan animasi: 'animate-fade-in' untuk notifikasi status --}}
                    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 animate-fade-in" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    {{-- Penambahan animasi: 'animate-shake' untuk notifikasi error --}}
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 animate-shake" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.verify') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="identifier" class="block text-sm font-medium text-gray-700">NIM atau Username</label>
                        {{-- Penambahan transisi pada input field saat focus --}}
                        <input id="identifier" type="text" class="mt-1 w-full px-4 py-3 bg-gray-100 text-gray-800 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out transform focus:scale-105 @error('identifier') border-red-500 @enderror" name="identifier" value="{{ old('identifier') }}" required autocomplete="identifier" autofocus placeholder="Masukkan NIM atau Username Anda">
                        @error('identifier')
                            <span class="text-red-500 text-sm mt-2" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div>
                        {{-- Penambahan efek hover yang lebih dinamis --}}
                        <button type="submit" class="w-full bg-[#FBBF24] text-[#1E3A8A] font-bold py-3 rounded-lg hover:bg-yellow-400 transition duration-300 ease-in-out shadow-lg transform hover:-translate-y-1 hover:shadow-xl">
                            Verifikasi Akun
                        </button>
                    </div>

                    <div class="text-center">
                        {{-- Penambahan transisi warna pada link --}}
                        <a class="text-sm text-blue-600 hover:underline hover:text-blue-800 transition-colors duration-300" href="{{ route('login') }}">
                            Kembali ke Login
                        </a>
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
@endsection

{{-- START: Penambahan blok style untuk definisi animasi --}}
<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-fade-in {
        animation: fadeIn 1s ease-in-out forwards;
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