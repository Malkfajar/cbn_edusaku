@extends('layouts.dashboard')

@section('title', 'Profile Saya')

@section('content')
{{-- Tambahkan x-init untuk animasi awal saat halaman dimuat --}}
<div class="bg-white rounded-lg shadow-md p-6 md:p-8 max-w-4xl mx-auto" x-data="{ editMode: false, photoName: null, photoPreview: null, passwordModalOpen: @json($errors->has('current_password') || $errors->has('new_password')),showCurrentPassword: false, showNewPassword: false, showConfirmPassword: false}"x-init="$el.classList.add('animate-fade-in-up')">
    <h2 class="text-2xl font-bold text-[#0b3d91] text-center mb-4">Profile Saya</h2>

    {{-- Animasi untuk notifikasi status, akan hilang setelah 5 detik --}}
    @if (session('status'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('status') }}</span>
        </div>
    @endif

    @if (session('password_status'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('password_status') }}</span>
        </div>
    @endif

    <div>
        <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex flex-col items-center space-y-4">
                <div class="relative">
                    {{-- Tambahkan transisi saat mengganti foto --}}
                    <img x-show="!photoPreview" x-transition.opacity src="{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : 'https://placehold.co/128x128/E2E8F0/A0AEC0?text=Foto' }}" alt="Current Profile Photo" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg transition-transform transform hover:scale-105">
                    <div x-show="photoPreview" class="relative" x-transition.opacity>
                        <span class="block w-32 h-32 rounded-full bg-cover bg-no-repeat bg-center" :style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <input type="file" name="photo" id="photo" class="hidden" x-ref="photo" @change=" photoName = $refs.photo.files[0].name; const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result; }; reader.readAsDataURL($refs.photo.files[0]);" />
                    {{-- Tambahkan animasi pada tombol --}}
                    <button type="button" x-on:click.prevent="$refs.photo.click()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 text-sm transition-all transform hover:scale-105 active:scale-95">
                        Pilih Foto Baru
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#0b3d91] text-white rounded-md hover:bg-[#1e4e9c] text-sm transition-all transform hover:scale-105 active:scale-95" x-show="photoPreview" x-transition x-cloak>
                        Simpan Foto
                    </button>
                </div>
                @error('photo')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
        </form>
    </div>

    <div class="border-t my-8"></div>

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">Detail Informasi</h3>
            <div class="flex items-center space-x-2">
                <button type="button" @click="editMode = true" x-show="!editMode" x-cloak class="p-2 rounded-full hover:bg-gray-200 transition-colors transform hover:rotate-12">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 text-base">
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Nama</p>
                    <p class="font-semibold text-lg text-gray-400 bg-gray-100 p-3 rounded-md truncate">{{ $user->name }}</p>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    {{-- Tambahkan transisi antara mode lihat dan mode edit --}}
                    <div class="relative min-h-[50px]">
                        <p x-show="!editMode" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute font-semibold text-lg text-gray-800 p-3 truncate">{{ $user->email }}</p>
                        <input x-show="editMode" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <p class="text-sm text-gray-500">Program</p>
                    <p class="font-semibold text-lg text-gray-400 bg-gray-100 p-3 rounded-md">{{ $user->program ?? 'Belum diatur' }}</p>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Tahun Masuk</p>
                    <p class="font-semibold text-lg text-gray-400 bg-gray-100 p-3 rounded-md">{{ $user->tahun_masuk ?? 'Belum diatur' }}</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500">Tanggal Lahir</p>
                    <p class="font-semibold text-lg text-gray-400 bg-gray-100 p-3 rounded-md">{{ $user->tanggal_lahir ? $user->tanggal_lahir->format('d F Y') : 'Belum diatur' }}</p>
                </div>
                
                <div>
                    <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                    {{-- Tambahkan transisi antara mode lihat dan mode edit --}}
                    <div class="relative min-h-[50px]">
                        <p x-show="!editMode" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute font-semibold text-lg text-gray-800 p-3">{{ $user->no_telepon ?? 'Belum diatur' }}</p>
                        <input x-show="editMode" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak type="tel" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" placeholder="Isi nomor telepon..." class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    @error('no_telepon') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Tambahkan animasi bertingkat pada tombol aksi --}}
        <div x-show="editMode" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="mt-8 flex justify-end space-x-3">
            <button type="button" @click="editMode = false; window.location.reload();" class="px-6 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg shadow-md hover:bg-gray-300 focus:outline-none transition-all transform hover:scale-105 active:scale-95">
                Batal
            </button>
            <button type="submit" class="px-6 py-2 bg-[#0b3d91] text-white font-semibold rounded-lg shadow-md hover:bg-[#1e4e9c] focus:outline-none transition-all transform hover:scale-105 active:scale-95">
                Simpan Perubahan
            </button>
            <button type="button" @click="passwordModalOpen = true" class="px-6 py-2 bg-[#FBBF24] text-[#1E3A8A] hover:bg-yellow-400 font-semibold rounded-lg shadow-md transition-all transform hover:scale-105 active:scale-95">
                Ubah Password
            </button>
        </div>
    </form>

    {{-- Tambahkan animasi yang lebih baik pada modal --}}
    <div x-show="passwordModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
        <div @click.away="passwordModalOpen = false" class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md mx-4" x-show="passwordModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf
                <h3 class="text-xl font-bold text-gray-800 mb-6">Ubah Password</h3>
                <div class="space-y-4">
                    <div class="relative">
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                        <input :type="showCurrentPassword ? 'text' : 'password'" id="current_password" name="current_password" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-10">
                        <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute inset-y-0 right-0 top-6 pr-3 flex items-center text-gray-500">
                            <svg x-show="!showCurrentPassword" class="h-5 w-5 icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="showCurrentPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.774 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243l-4.243-4.243" /></svg>
                        </button>
                    </div>
                     @error('current_password')
                        <p class="text-red-500 text-xs mt-1 -translate-y-3">{{ $message }}</p>
                    @enderror

                    <div class="relative">
                        <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                        <input :type="showNewPassword ? 'text' : 'password'" id="new_password" name="new_password" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-10">
                        <button type="button" @click="showNewPassword = !showNewPassword" class="absolute inset-y-0 right-0 top-6 pr-3 flex items-center text-gray-500">
                            <svg x-show="!showNewPassword" class="h-5 w-5 icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="showNewPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.774 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243l-4.243-4.243" /></svg>
                        </button>
                    </div>
                     @error('new_password')
                        <p class="text-red-500 text-xs mt-1 -translate-y-3">{{ $message }}</p>
                    @enderror

                    <div class="relative">
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                        <input :type="showConfirmPassword ? 'text' : 'password'" id="new_password_confirmation" name="new_password_confirmation" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm pr-10">
                        <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 top-6 pr-3 flex items-center text-gray-500">
                           <svg x-show="!showConfirmPassword" class="h-5 w-5 icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="showConfirmPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.774 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243l-4.243-4.243" /></svg>
                        </button>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" @click="passwordModalOpen = false" class="px-6 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg shadow-md hover:bg-gray-300 focus:outline-none transition-all transform hover:scale-105 active:scale-95">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2 bg-[#0b3d91] text-white font-semibold rounded-lg shadow-md hover:bg-[#1e4e9c] focus:outline-none transition-all transform hover:scale-105 active:scale-95">
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Menambahkan keyframes untuk animasi jika belum ada di CSS global --}}
@push('styles')
<style>
    @keyframes fade-in-up {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fade-in-up 0.5s ease-out forwards;
    }
</style>
@endpush

@endsection

