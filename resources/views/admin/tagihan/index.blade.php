@extends('admin.layouts.app')

@section('title', 'Manajemen Tagihan')

@section('content')

<style>
    /* PERUBAHAN: CSS untuk .pagination-custom telah dihapus */

    .status-label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 9999px;
        text-transform: capitalize;
        white-space: nowrap;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: transform 0.2s ease-in-out; 
    }
    .status-label:hover {
        transform: scale(1.05); 
    }

    /* Menggunakan class lowercase agar lebih konsisten dengan data enum */
    .status-label.lunas { background-color: #d1fae5; color: #047857; }
    .status-label.pending { background-color: #fefcbf; color: #a16207; }
    .status-label.belum-lunas { background-color: #fed7aa; color: #c2410c; }
    .status-label.ditolak { background-color: #fee2e2; color: #dc2626; }
    .status-label.belum-dibayar { background-color: #ef8484; color: #8a213f; }


    @media (max-width: 640px) {
        .status-label { padding: 0.15rem 0.5rem; font-size: 0.65rem; }
        td, th { padding: 0.5rem; }
        .status-label.belum-dibayar { max-width: 80px; }
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.5s ease-out forwards;
    }
    
    .table-row-animated {
        opacity: 0;
        animation: fadeInUp 0.5s ease-out forwards;
    }
</style>

<div x-data="{ addModalOpen: false, editModalOpen: false, bulkAddModalOpen: false, importModalOpen: false, selectedTagihan: null }">
    <div class="bg-white p-6 rounded-lg shadow-md animate-fadeInUp">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b">
            <div class="animate-fadeInUp" style="animation-delay: 100ms;">
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Tagihan</h2>
                <p class="text-sm text-gray-500 mt-1">Buat, lihat, dan kelola semua tagihan mahasiswa.</p>
            </div>
           <div class="mt-4 sm:mt-0 flex flex-wrap items-center justify-start sm:justify-end gap-2 animate-fadeInUp" style="animation-delay: 200ms;">
                <form action="{{ route('admin.tagihan.index') }}" method="GET" class="flex-grow sm:flex-grow-0">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari..." class="w-full sm:w-40 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300">
                </form>
                <button @click="importModalOpen = true" class="flex-shrink-0 bg-teal-600 text-white p-2 rounded-lg hover:bg-teal-700 flex items-center justify-center transition-transform transform hover:scale-110" title="Import Tagihan"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg></button>
                <button @click="bulkAddModalOpen = true" class="flex-shrink-0 bg-purple-600 text-white p-2 rounded-lg hover:bg-purple-700 flex items-center justify-center transition-transform transform hover:scale-110" title="Buat Tagihan Massal"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"></path></svg></button>
                <button @click="addModalOpen = true" class="flex-shrink-0 bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 flex items-center justify-center transition-transform transform hover:scale-110" title="Tambah Tagihan Baru"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg></button>
            </div>
        </div>

        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-4" class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                <p>{{ session('status') }}</p>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 rounded-l-lg">Mahasiswa</th>
                        <th scope="col" class="px-6 py-3">Nama Tagihan</th>
                        <th scope="col" class="px-6 py-3">Jumlah</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3 text-center rounded-r-lg">Aksi</th>
                    </tr>
                </thead>
               <tbody>
                @forelse($tagihan as $t)
                <tr class="bg-white border-b hover:bg-gray-50 table-row-animated" style="animation-delay: {{ $loop->index * 70 }}ms">
                    <td class="px-6 py-4 font-medium text-gray-900">
                        <p class="font-semibold">{{ $t->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $t->user->nim }}</p>
                    </td>
                    <td class="px-6 py-4">{{ $t->nama_tagihan }} - Smst. {{ $t->semester }}</td>
                   <td class="px-6 py-4">
                        @if($t->izinkan_cicilan && $t->sisa_tagihan < $t->jumlah_total)
                            {{-- Tampilan jika cicilan diizinkan DAN sudah ada pembayaran masuk --}}
                            <div>
                                <p class="font-semibold text-gray-800">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">dari Rp {{ number_format($t->jumlah_total, 0, ',', '.') }}</p>
                            </div>
                        @else
                            {{-- Tampilan default jika wajib lunas atau cicilan belum dibayar --}}
                            <p>Rp {{ number_format($t->jumlah_total, 0, ',', '.') }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="status-label {{ str_replace('_', '-', $t->status) }}" title="{{ $t->status }}">
                            {{ str_replace('_', ' ', $t->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center space-x-2">
                            <button @click="editModalOpen = true; selectedTagihan = {{ json_encode($t) }}" class="p-2 text-blue-600 hover:bg-blue-100 rounded-full transition-transform transform hover:scale-125 hover:rotate-6" title="Edit Tagihan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </button>
                            <form action="{{ route('admin.tagihan.toggleInstallment', $t->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 rounded-full transition-transform transform hover:scale-125" 
                                        title="{{ $t->izinkan_cicilan ? 'Nonaktifkan Cicilan' : 'Aktifkan Cicilan' }}">
                                    @if($t->izinkan_cicilan)
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                    @endif
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 animate-fadeInUp">Tidak ada data tagihan.</td></tr>
                @endforelse
               </tbody>
            </table>
        </div>
        
        {{-- PERUBAHAN: Blok paginasi kustom sesuai desain --}}
        @if ($tagihan->hasPages())
        <div class="mt-6">
            <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center">
                <div class="flex items-center space-x-2">
                    {{-- Previous Page Link --}}
                    @if ($tagihan->onFirstPage())
                        <span class="flex items-center justify-center w-10 h-10 text-gray-400 bg-white border border-gray-200 rounded-md cursor-not-allowed">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </span>
                    @else
                        <a href="{{ $tagihan->previousPageUrl() }}" rel="prev" class="flex items-center justify-center w-10 h-10 text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($tagihan->links()->elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="flex items-center justify-center w-10 h-10 text-gray-500 bg-white border border-gray-200 rounded-md cursor-default">{{ $element }}</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $tagihan->currentPage())
                                    <span class="flex items-center justify-center w-10 h-10 text-white bg-orange-500 border border-orange-500 rounded-md cursor-default">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="flex items-center justify-center w-10 h-10 text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-100 transition">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($tagihan->hasMorePages())
                        <a href="{{ $tagihan->nextPageUrl() }}" rel="next" class="flex items-center justify-center w-10 h-10 text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        </a>
                    @else
                        <span class="flex items-center justify-center w-10 h-10 text-gray-400 bg-white border border-gray-200 rounded-md cursor-not-allowed">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        </span>
                    @endif
                </div>
            </nav>
        </div>
        @endif
    </div>

    <div x-show="addModalOpen"  x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50" x-cloak>
        <div @click.away="addModalOpen = false" class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-auto" x-show="addModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
            <form action="{{ route('admin.tagihan.store') }}" method="POST">
                @csrf
                <div class="p-6 border-b"><h3 class="text-xl font-bold text-gray-800">Tambah Tagihan Baru</h3></div>
                <div class="p-6 space-y-4">
                    <div><label for="user_id" class="block text-sm font-medium text-gray-700">Mahasiswa</label><select name="user_id" id="user_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><option value="">Pilih Mahasiswa</option>@foreach($mahasiswa as $mhs)<option value="{{ $mhs->id }}">{{ $mhs->name }} ({{ $mhs->nim }})</option>@endforeach</select></div>
                    <div><label for="nama_tagihan" class="block text-sm font-medium text-gray-700">Nama Tagihan</label><input type="text" name="nama_tagihan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: UKT Ganjil"></div>
                    <div><label for="semester" class="block text-sm font-medium text-gray-700">Semester</label><input type="number" name="semester" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: 5"></div>
                    <div>
                        <label for="jumlah_total_display" class="block text-sm font-medium text-gray-700">Jumlah</label>
                        <input type="text" id="jumlah_total_display" @input="formatRupiah($event, 'jumlah_total_hidden')" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Rp 5.000.000">
                        <input type="hidden" name="jumlah_total" id="jumlah_total_hidden">
                    </div>
                </div>
                <div class="p-6 bg-gray-50 flex justify-end space-x-3"><button type="button" @click="addModalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Batal</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700">Simpan Tagihan</button></div>
            </form>
        </div>
    </div>
    
    <div x-show="editModalOpen"  x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50" x-cloak>
        <div @click.away="editModalOpen = false" class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-auto" x-show="editModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
            <template x-if="selectedTagihan">
                <div x-data="{ editNominal: new Intl.NumberFormat('id-ID', { style: 'decimal' }).format(selectedTagihan.jumlah_total) }">
                    <form :action="`/admin/tagihan/${selectedTagihan.id}`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="p-6 border-b"><h3 class="text-xl font-bold text-gray-800">Edit Tagihan</h3></div>
                        <div class="p-6 space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700">Mahasiswa</label><p class="mt-1 p-2 bg-gray-100 rounded-md" x-text="selectedTagihan.user.name + ' (' + selectedTagihan.user.nim + ')'"></p><input type="hidden" name="user_id" :value="selectedTagihan.user_id"></div>
                            <div><label for="edit_nama_tagihan" class="block text-sm font-medium text-gray-700">Nama Tagihan</label><input type="text" name="nama_tagihan" id="edit_nama_tagihan" :value="selectedTagihan.nama_tagihan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></div>
                            <div><label for="edit_semester" class="block text-sm font-medium text-gray-700">Semester</label><input type="number" name="semester" id="edit_semester" :value="selectedTagihan.semester" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></div>
                            <div>
                                <label for="edit_jumlah_total_display" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                                <input type="text" id="edit_jumlah_total_display" x-model="editNominal" @input="formatRupiah($event, 'edit_jumlah_total_hidden')" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <input type="hidden" name="jumlah_total" id="edit_jumlah_total_hidden" :value="selectedTagihan.jumlah_total">
                            </div>
                        </div>
                        <div class="p-6 bg-gray-50 flex justify-end space-x-3"><button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Batal</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700">Simpan Perubahan</button></div>
                    </form>
                </div>
            </template>
        </div>
    </div>  
    <div x-show="bulkAddModalOpen"  x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50" x-cloak>
        <div @click.away="bulkAddModalOpen = false" class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-auto" x-show="bulkAddModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
            <form action="{{ route('admin.tagihan.storeBulk') }}" method="POST">
                @csrf
                <div class="p-6 border-b"><h3 class="text-xl font-bold text-gray-800">Buat Tagihan Massal</h3></div>
                <div class="p-6 space-y-4">
                    <div><label for="bulk_nama_tagihan" class="block text-sm font-medium text-gray-700">Nama Tagihan</label><input type="text" name="nama_tagihan" id="bulk_nama_tagihan" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: UKT Genap 2024/2025"></div>
                    <div>
                        <label for="bulk_jumlah_total_display" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                        <input type="text" id="bulk_jumlah_total_display" @input="formatRupiah($event, 'bulk_jumlah_total_hidden')" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Rp 5.000.000">
                        <input type="hidden" name="jumlah_total" id="bulk_jumlah_total_hidden">
                    </div>
                    <div><label for="bulk_semester" class="block text-sm font-medium text-gray-700">Untuk Semester</label><input type="number" name="semester" id="bulk_semester" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: 6"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label for="bulk_program" class="block text-sm font-medium text-gray-700">Untuk Program</label><select name="program" id="bulk_program" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><option value="semua">Semua Program</option>@foreach($programs as $program)<option value="{{ $program }}">{{ $program }}</option>@endforeach</select></div>
                        <div><label for="bulk_tahun_masuk" class="block text-sm font-medium text-gray-700">Untuk Angkatan</label><select name="tahun_masuk" id="bulk_tahun_masuk" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><option value="semua">Semua Angkatan</option>@foreach($angkatans as $angkatan)<option value="{{ $angkatan }}">{{ $angkatan }}</option>@endforeach</select></div>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 flex justify-end space-x-3"><button type="button" @click="bulkAddModalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Batal</button><button type="submit" class="px-4 py-2 bg-purple-600 text-white font-semibold rounded-lg shadow-md hover:bg-purple-700">Buat Tagihan</button></div>
            </form>
        </div>
    </div>
    
    <div x-show="importModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50" x-cloak>
        <div @click.away="importModalOpen = false" class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-auto">
            <form action="{{ route('admin.tagihan.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 border-b"><h3 class="text-xl font-bold text-gray-800">Import Tagihan dari CSV</h3></div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700">Pilih File CSV</label>
                        <input type="file" name="file" id="file" required accept=".csv" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100"/>
                        <p class="text-xs text-gray-500 mt-2">Pastikan file CSV memiliki kolom dengan urutan: <br><strong class="font-mono">nim, semester, nama_tagihan, jumlah_total, status</strong></p>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 flex justify-end space-x-3"><button type="button" @click="importModalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Batal</button><button type="submit" class="px-4 py-2 bg-teal-600 text-white font-semibold rounded-lg shadow-md hover:bg-teal-700">Import Data</button></div>
            </form>
        </div>
    </div>
</div> 
<script>
    function formatRupiah(event, hiddenInputId) {
        let value = event.target.value.replace(/[^,\d]/g, '').toString();
        let hiddenInput = document.getElementById(hiddenInputId);
        if(hiddenInput) { hiddenInput.value = value.replace(/[^0-9]/g, ''); }
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        if (ribuan) { let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        event.target.value = rupiah ? 'Rp ' + rupiah : '';
    }
</script>
@endsection