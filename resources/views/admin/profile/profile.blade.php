@extends('admin.layouts.app')

@section('title', 'Profile Saya')

@section('content')
{{-- Penambahan <style> untuk animasi custom (opsional, tapi membuat efek lebih kaya) --}}
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
    .stagger-1 { animation-delay: 100ms; }
    .stagger-2 { animation-delay: 200ms; }
</style>

<div x-data="{ isEditing: false, showPasswordModal: false }" class="max-w-4xl mx-auto space-y-8">

    {{-- Notifikasi Sukses Global --}}
    @if (session('status'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 5000)"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative"
             role="alert">
            <span class="block sm:inline">{{ session('status') }}</span>
        </div>
    @endif
    {{-- 1. KARTU UBAH FOTO PROFIL --}}
    {{-- ANIMASI: Efek "lift" saat di-hover dan staggered fade-in --}}
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-md transition duration-300 hover:shadow-xl hover:-translate-y-1 animate-fade-in stagger-1">
        <h2 class="text-2xl font-bold text-gray-800 border-b pb-4 mb-6">Foto Profil</h2>
        <div x-data="{ newPhotoUrl: null }" class="flex flex-col items-center text-center">
            <form id="form-photo-upload" method="POST" action="{{ route('admin.profile.photo.update') }}" enctype="multipart/form-data" class="w-full">
                @csrf
                <div class="relative inline-block mb-4 group">
                    {{-- ANIMASI: Gambar membesar saat di-hover --}}
                    <img :src="newPhotoUrl || '{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('images/default-avatar.png') }}'" alt="Foto Profil" class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg transition-transform duration-300 group-hover:scale-105">
                </div>
                <input type="file" name="photo" class="hidden" x-ref="photoInput" @change="newPhotoUrl = URL.createObjectURL($event.target.files[0])" accept="image/*">
                <div class="flex justify-center items-center gap-4 mt-2">
                    {{-- ANIMASI: Tombol memantul saat di-hover --}}
                    <button @click.prevent="$refs.photoInput.click()" x-show="!newPhotoUrl" type="button" class="px-5 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-200 hover:scale-105 active:scale-95">Pilih Foto Baru</button>
                    
                    {{-- ANIMASI: Transisi pop-in untuk tombol baru --}}
                    <div x-show="newPhotoUrl" 
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 scale-90"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-200 transform"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-90"
                         class="flex items-center gap-4">
                        <button @click.prevent="newPhotoUrl = null; $refs.photoInput.value = ''" type="button" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition hover:scale-105 active:scale-95">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition hover:scale-105 active:scale-95">Simpan Foto</button>
                    </div>
                </div>
                @error('photo') <span class="text-red-500 text-xs mt-2 d-block">{{ $message }}</span> @enderror
            </form>
        </div>
    </div>

    {{-- 2. KARTU EDIT DETAIL INFORMASI --}}
    {{-- ANIMASI: Efek "lift" saat di-hover dan staggered fade-in --}}
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-md transition duration-300 hover:shadow-xl hover:-translate-y-1 animate-fade-in stagger-2">
        <form method="POST" action="{{ route('admin.profile.update') }}">
            @csrf
            <div class="flex justify-between items-center border-b pb-4 mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Detail Informasi</h2>
                <button @click.prevent="isEditing = true" x-show="!isEditing" type="button" class="p-2 text-gray-500 hover:text-blue-600 rounded-full hover:bg-gray-100 transition-transform duration-300 hover:rotate-12" title="Edit Profil">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z"></path></svg>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 text-sm">
                <div>
                    <label for="name" class="block font-medium text-gray-600 mb-1">Nama</label>
                    {{-- ANIMASI: Efek border dan shadow saat input aktif --}}
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" :disabled="!isEditing" class="w-full p-3 rounded-lg border focus:ring-2 transition duration-200" :class="isEditing ? 'bg-white border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-200' : 'bg-gray-100 border-gray-200 cursor-not-allowed'">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="email" class="block font-medium text-gray-600 mb-1">Email</label>
                    {{-- ANIMASI: Efek border dan shadow saat input aktif --}}
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" :disabled="!isEditing" class="w-full p-3 rounded-lg border focus:ring-2 transition duration-200" :class="isEditing ? 'bg-white border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-200' : 'bg-gray-100 border-gray-200 cursor-not-allowed'">
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            {{-- ANIMASI: Transisi geser dan fade untuk tombol aksi --}}
            <div x-show="isEditing" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-4"
                 class="flex flex-wrap justify-end items-center gap-3 mt-8 pt-4 border-t">
                <button @click.prevent="isEditing = false" type="button" class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold transition transform hover:scale-105">Batal</button>
                <button @click.prevent="showPasswordModal = true" type="button" class="px-6 py-2 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-[#1E3A8A] font-semibold transition shadow-md transform hover:scale-105">Ubah Password</button>
                <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition shadow-md transform hover:scale-105">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    {{-- 3. MODAL / POP-UP UNTUK UBAH PASSWORD --}}
    {{-- ANIMASI: Latar belakang fade-in, panel modal slide-up dan memantul --}}
    <div x-show="showPasswordModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50">
        <div @click.away="showPasswordModal = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 -translate-y-10"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 -translate-y-10"
             class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Ubah Password</h3>
                <button @click="showPasswordModal = false" class="p-1 rounded-full transition-transform duration-300 hover:bg-gray-200 hover:rotate-90">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.profile.password.update') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block font-medium text-gray-600 mb-1 text-sm">Password Saat Ini</label>
                        <div x-data="{ show: false }" class="relative">
                            <input :type="show ? 'text' : 'password'" id="current_password" name="current_password" required class="w-full p-3 pr-10 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 transition-shadow duration-200">
                            <button @click="show = !show" type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        @error('current_password', 'updatePassword') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                     <div>
                        <label for="password" class="block font-medium text-gray-600 mb-1 text-sm">Password Baru</label>
                         <div x-data="{ show: false }" class="relative">
                            <input :type="show ? 'text' : 'password'" id="password" name="password" required class="w-full p-3 pr-10 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 transition-shadow duration-200">
                            <button @click="show = !show" type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        @error('password', 'updatePassword') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                     <div>
                        <label for="password_confirmation" class="block font-medium text-gray-600 mb-1 text-sm">Konfirmasi Password Baru</label>
                         <div x-data="{ show: false }" class="relative">
                            <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required class="w-full p-3 pr-10 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 transition-shadow duration-200">
                            <button @click="show = !show" type="button" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition shadow-md transform hover:scale-105 active:scale-95">
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection