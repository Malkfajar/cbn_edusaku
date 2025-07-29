@extends('admin.layouts.app')

@section('title', 'Manajemen Mahasiswa')

@section('content')

{{-- Penambahan: Alpine.js untuk mengelola state animasi saat load & modal --}}
<div x-data="{ editModalOpen: false, addModalOpen: false, deleteModalOpen: false, selectedUser: null, pageLoaded: false }"
     x-init="requestAnimationFrame(() => pageLoaded = true)">
    <div class="bg-white p-6 rounded-lg shadow-md">
        {{-- Penambahan: Animasi fade-in dan geser saat halaman dimuat --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b transition-all duration-500 ease-out"
             :class="pageLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-4'">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Mahasiswa</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data, edit, tambah, atau hapus data mahasiswa.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                <form action="{{ route('admin.mahasiswa.index') }}" method="GET" class="flex items-center space-x-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari mahasiswa..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-shadow">
                </form>
                {{-- Penambahan: Animasi hover pada tombol --}}
                <button @click="addModalOpen = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center space-x-2 transition-all duration-200 hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span>Tambah</span>
                </button>
            </div>
        </div>

        {{-- Penambahan: Animasi untuk notifikasi --}}
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 5000)" class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                <p>{{ session('status') }}</p>
            </div>
        @elseif (session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 5000)" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 rounded-l-lg">Nama</th>
                        <th scope="col" class="px-6 py-3">NIM</th>
                        <th scope="col" class="px-6 py-3">Intake</th>
                        <th scope="col" class="px-6 py-3">Program</th>
                        <th scope="col" class="px-6 py-3 text-center rounded-r-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswa as $mhs)
                    {{-- Penambahan: Animasi baris tabel saat load, dengan delay bertahap --}}
                    <tr class="bg-white border-b hover:bg-gray-50 transition-all duration-300 ease-out"
                        :class="pageLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                        style="transition-delay: {{ 100 + ($loop->index * 50) }}ms">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                           <div class="flex items-center">
                                <img class="w-10 h-10 rounded-full object-cover mr-4" src="{{ $mhs->profile_photo_path ? asset('storage/' . $mhs->profile_photo_path) : 'https://placehold.co/40x40/E2E8F0/A0AEC0?text=' . substr($mhs->name, 0, 1) }}" alt="Foto profil">
                                {{ $mhs->name }}
                           </div>
                        </td>
                        <td class="px-6 py-4">{{ $mhs->nim }}</td>
                        <td class="px-6 py-4">{{ $mhs->tahun_masuk }}</td>
                        <td class="px-6 py-4">{{ $mhs->program ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center items-center space-x-2">
                                {{-- Penambahan: Animasi hover pada tombol aksi --}}
                                <button @click="editModalOpen = true; selectedUser = {{ json_encode($mhs) }}" class="p-2 text-blue-600 hover:bg-blue-100 rounded-full transition-transform duration-200 hover:scale-125" title="Edit Mahasiswa">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <button @click="deleteModalOpen = true; selectedUser = {{ json_encode($mhs) }}" class="p-2 text-red-600 hover:bg-red-100 rounded-full transition-transform duration-200 hover:scale-125" title="Hapus Mahasiswa">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data mahasiswa ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PERUBAHAN: Paginasi kustom sesuai desain gambar --}}
        @if ($mahasiswa->hasPages())
        <div class="mt-6 transition-all duration-500 ease-out"
             :class="pageLoaded ? 'opacity-100' : 'opacity-0'" style="transition-delay: {{ 150 + ($mahasiswa->count() * 50) }}ms">
            <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
                <div class="flex-1 flex items-center justify-center">
                    <div class="flex items-center space-x-2">
                        {{-- Previous Page Link --}}
                        @if ($mahasiswa->onFirstPage())
                            <span class="flex items-center justify-center w-10 h-10 text-gray-400 bg-white border border-gray-200 rounded-md cursor-not-allowed">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @else
                            <a href="{{ $mahasiswa->previousPageUrl() }}" class="flex items-center justify-center w-10 h-10 text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-100 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @endif
                        
                        {{-- Pagination Elements --}}
                        @foreach ($mahasiswa->links()->elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <span class="flex items-center justify-center w-10 h-10 text-gray-500 bg-white border border-gray-200 rounded-md cursor-default">{{ $element }}</span>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $mahasiswa->currentPage())
                                        <span class="flex items-center justify-center w-10 h-10 text-white bg-[#0b3d91] border rounded-md cursor-default">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="flex items-center justify-center w-10 h-10 text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-100 transition">{{ $page }}</a>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($mahasiswa->hasMorePages())
                            <a href="{{ $mahasiswa->nextPageUrl() }}" class="flex items-center justify-center w-10 h-10 text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-100 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        @else
                            <span class="flex items-center justify-center w-10 h-10 text-gray-400 bg-white border border-gray-200 rounded-md cursor-not-allowed">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </nav>
        </div>
        @endif

    </div>

    <div x-show="addModalOpen" 
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
         {{-- PERUBAHAN: 'items-center' diubah menjadi 'items-start' agar modal muncul di atas --}}
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-16 bg-black bg-opacity-50" x-cloak>
        <div @click.away="addModalOpen = false" 
             x-show="addModalOpen" 
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="bg-white rounded-lg shadow-2xl w-full max-w-2xl mx-auto max-h-[90vh] overflow-y-auto">
            <form action="{{ route('admin.mahasiswa.store') }}" method="POST">
                @csrf
                <div class="p-6 border-b sticky top-0 bg-white z-10"><h3 class="text-xl font-bold text-gray-800">Tambah Mahasiswa Baru</h3></div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label for="add_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label><input type="text" name="name" id="add_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="add_nim" class="block text-sm font-medium text-gray-700">NIM</label><input type="text" name="nim" id="add_nim" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="add_email" class="block text-sm font-medium text-gray-700">Email</label><input type="email" name="email" id="add_email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="add_password" class="block text-sm font-medium text-gray-700">Password</label><input type="password" name="password" id="add_password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="add_program" class="block text-sm font-medium text-gray-700">Program</label><input type="text" name="program" id="add_program" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="add_tahun_masuk" class="block text-sm font-medium text-gray-700">Tahun Masuk</label><input type="text" name="tahun_masuk" id="add_tahun_masuk" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 2023"></div>
                    <div><label for="add_tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label><input type="date" name="tanggal_lahir" id="add_tanggal_lahir" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                    <div><label for="add_no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label><input type="tel" name="no_telepon" id="add_no_telepon" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                </div>
                <div class="p-6 bg-gray-50 flex justify-end space-x-3 sticky bottom-0 z-10"><button type="button" @click="addModalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">Batal</button><button type="submit" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition-colors">Simpan Mahasiswa</button></div>
            </form>
        </div>
    </div>
    
    <div x-show="editModalOpen" 
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
         {{-- PERUBAHAN: 'items-center' diubah menjadi 'items-start' agar modal muncul di atas --}}
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-16 bg-black bg-opacity-50" x-cloak>
        <div @click.away="editModalOpen = false" 
             x-show="editModalOpen"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-lg shadow-2xl w-full max-w-2xl mx-auto max-h-[90vh] overflow-y-auto">
            <template x-if="selectedUser">
                <form :action="`/admin/mahasiswa/${selectedUser.id}`" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 border-b sticky top-0 bg-white z-10"><h3 class="text-xl font-bold text-gray-800">Edit Data Mahasiswa</h3></div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div><label for="edit_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label><input type="text" name="name" id="edit_name" :value="selectedUser.name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label for="edit_nim" class="block text-sm font-medium text-gray-700">NIM</label><input type="text" name="nim" id="edit_nim" :value="selectedUser.nim" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label for="edit_email" class="block text-sm font-medium text-gray-700">Email</label><input type="email" name="email" id="edit_email" :value="selectedUser.email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label for="edit_program" class="block text-sm font-medium text-gray-700">Program</label><input type="text" name="program" id="edit_program" :value="selectedUser.program" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label for="edit_tahun_masuk" class="block text-sm font-medium text-gray-700">Tahun Masuk</label><input type="text" name="tahun_masuk" id="edit_tahun_masuk" :value="selectedUser.tahun_masuk" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 2023"></div>
                        <div><label for="edit_tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label><input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" :value="selectedUser.tanggal_lahir" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                        <div class="md:col-span-2"><label for="edit_no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label><input type="tel" name="no_telepon" id="edit_no_telepon" :value="selectedUser.no_telepon" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></div>
                    </div>
                    <div class="p-6 bg-gray-50 flex justify-end space-x-3 sticky bottom-0 z-10"><button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">Batal</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-colors">Simpan Perubahan</button></div>
                </form>
            </template>
        </div>
    </div>

    <div x-show="deleteModalOpen" 
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50" x-cloak>
        <div @click.away="deleteModalOpen = false"
             x-show="deleteModalOpen"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-auto">
            <template x-if="selectedUser">
                <form :action="`/admin/mahasiswa/${selectedUser.id}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="p-6 text-center">
                        <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <h3 class="mt-5 text-lg font-medium text-gray-900">Konfirmasi Hapus</h3>
                        <div class="mt-2 text-sm text-gray-500">
                            <p>Anda yakin ingin menghapus data mahasiswa:</p>
                            <p class="font-semibold" x-text="selectedUser.name"></p>
                            <p class="mt-1">Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 flex justify-center space-x-4">
                        <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 transition-colors">Ya, Hapus</button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
@endsection