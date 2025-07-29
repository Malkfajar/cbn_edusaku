@extends('layouts.dashboard')

@section('content')
<style>
    /* Keyframes yang sudah ada */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-fade-in {
        /* Menambahkan 'forwards' agar state akhir animasi tetap terjaga */
        animation: fadeIn 1s ease-in-out forwards;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .animate-slide-up {
        animation: slideUp 1s ease-in-out forwards;
    }

    @keyframes slideDown {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .animate-slide-down {
        animation: slideDown 0.5s ease-out;
    }

    @keyframes pulseHover {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    .animate-pulse-hover:hover {
        animation: pulseHover 0.3s ease-in-out;
    }

    /* Penambahan animasi baru: shake */
    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }
    .animate-shake {
        animation: shake 0.6s cubic-bezier(.36,.07,.19,.97) both;
    }

    /* Style label status yang sudah ada */
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
    
    .status-label.lunas { background-color: #d1fae5; color: #047857; }
    .status-label.pending { background-color: #fefcbf; color: #a16207; }
    .status-label.belum-lunas { background-color: #fed7aa; color: #c2410c; }
    .status-label.ditolak { background-color: #fee2e2; color: #dc2626; }
    .status-label.belum-dibayar { background-color: #ef8484; color: #8a213f; }
</style>

<div class="animate-fade-in">
    {{-- Animasi 'slide-up' pada sapaan --}}
    <h2 class="text-xl md:text-2xl font-bold text-[#0b3d91] animate-slide-up">Halo, {{ explode(' ', $user->name)[0] }}!</h2>
    
    @if (session('status'))
        <div class="mt-4 mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative animate-fade-in" role="alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <span class="block sm:inline">{{ session('status') }}</span>
        </div>
    @endif

    {{-- Penambahan delay pada animasi 'slide-up' untuk efek berurutan --}}
    <div class="mt-4 p-4 md:p-6 bg-white rounded-lg shadow-md animate-slide-up" style="animation-delay: 0.2s;">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Info Mahasiswa dengan delay animasi berurutan --}}
            <div class="flex items-center animate-fade-in" style="animation-delay: 0.4s;">
                <svg class="w-8 h-8 text-green-500 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <div>
                    <p class="text-sm text-gray-500">Nama Mahasiswa</p>
                    <p class="font-semibold text-[#0b3d91] truncate">{{ $user->name }}</p>
                </div>
            </div>
            <div class="flex items-center animate-fade-in" style="animation-delay: 0.5s;">
                <svg class="w-8 h-8 text-gray-500 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <div>
                    <p class="text-sm text-gray-500">NIM</p>
                    <p class="font-semibold text-[#0b3d91]">{{ $user->nim }}</p>
                </div>
            </div>
            <div class="flex items-center animate-fade-in" style="animation-delay: 0.6s;">
                <svg class="w-8 h-8 text-gray-500 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-semibold text-[#0b3d91] truncate">{{ $user->email }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Penambahan delay pada animasi 'fade-in' --}}
    <div class="mt-6 p-4 bg-yellow-100 border-l-4 border-yellow-400 text-yellow-700 rounded-lg animate-fade-in" style="animation-delay: 0.7s;">
        <p class="text-sm">Apabila status pembayaran tidak berubah saat sudah melakukan pembayaran, silahkan uplad bukti pembayaran dan tunggu admin verifikasi dalam 1x24 jam.</p>
    </div>

    {{-- Penambahan delay pada animasi 'slide-up' --}}
    <form action="{{ route('payment.showDetails') }}" method="POST" onsubmit="return validateSelection()" class="mt-6 bg-white rounded-lg shadow-md animate-slide-up" style="animation-delay: 0.8s;">
        @csrf
        <div class="p-4 md:p-6">
            <h3 class="text-lg md:text-xl font-bold text-[#0b3d91]">Daftar Tagihan</h3>
            {{-- Penambahan animasi 'shake' saat error --}}
            <p id="selection-error" class="text-red-500 text-sm mt-2 hidden animate-shake">Silakan pilih minimal satu tagihan untuk melanjutkan.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="p-4 w-12 text-center">Pilih</th>
                        <th class="p-4">Semester</th>
                        <th class="p-4">Nama Tagihan</th>
                        <th class="p-4">Jumlah</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @php $firstUnpaidFound = false; @endphp
                    @forelse($tagihans as $tagihan)
                        @php
                            $isPayable = false;
                            if ($tagihan->status != 'lunas' && !$firstUnpaidFound) {
                                $isPayable = true;
                                $firstUnpaidFound = true;
                            }
                        @endphp
                        {{-- Penambahan animasi fade-in pada setiap baris dengan delay berurutan --}}
                        <tr class="hover:bg-gray-50 transition-colors duration-200 animate-fade-in" style="animation-delay: {{ 0.9 + ($loop->index * 0.05) }}s;">
                            <td class="p-4 text-center">
                                <input type="checkbox" name="tagihan[]" value="{{ $tagihan->id }}" 
                                       class="tagihan-checkbox" 
                                       @if(!$isPayable) disabled @endif>
                            </td>
                            <td class="p-4">{{ $tagihan->semester }}</td>
                            <td class="p-4">{{ $tagihan->nama_tagihan }}</td>
                            <td class="p-4">
                                @if($tagihan->izinkan_cicilan)
                                    <div>
                                        <p class="font-semibold">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</p>
                                        <p class="text-xs text-gray-500">dari total Rp {{ number_format($tagihan->jumlah_total, 0, ',', '.') }}</p>
                                    </div>
                                @else
                                    <p class="font-semibold">Rp {{ number_format($tagihan->jumlah_total, 0, ',', '.') }}</p>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="status-label {{ str_replace('_', '-', $tagihan->status) }}">
                                    {{ ucwords(str_replace('_', ' ', $tagihan->status)) }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center">
                                    @if($tagihan->status == 'lunas')
                                        {{-- Ikon Centang untuk status Lunas --}}
                                        <div title="Pembayaran Lunas">
                                            <svg class="w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @elseif($tagihan->status == 'belum_lunas')
                                        {{-- Ikon Jam Pasir untuk status Belum Lunas (Cicilan) --}}
                                        <div title="Proses Pembayaran Cicilan">
                                            <svg class="w-6 h-6 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @else
                                        {{-- Ikon Panah untuk status Belum Dibayar (Aksi selanjutnya adalah membayar) --}}
                                        <div title="Tagihan Siap Dibayar">
                                            <svg class="w-6 h-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 animate-fade-in">
                                Anda belum memiliki tagihan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 md:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-end">
            {{-- Penambahan efek hover yang lebih dinamis pada tombol --}}
            <button type="submit" class="w-full sm:w-auto text-center px-6 py-2.5 bg-[#0b3d91] text-white font-semibold rounded-lg shadow-md hover:bg-[#1e4e9c] transition duration-200 transform hover:-translate-y-0.5 animate-pulse-hover">Lanjutkan Pembayaran</button>
        </div>
    </form>
    
<script>
    function validateSelection() {
        const checked = document.querySelectorAll('.tagihan-checkbox:checked').length > 0;
        const errorEl = document.getElementById('selection-error');
            if (!checked) {
                errorEl.classList.remove('hidden');
                // Menambahkan kelas untuk memicu animasi shake
                errorEl.classList.add('animate-shake');
                // Menghapus kelas setelah animasi selesai agar bisa beranimasi lagi
                setTimeout(() => errorEl.classList.remove('animate-shake'), 600);
                return false;
            }
        errorEl.classList.add('hidden');
        return true;
    }
</script>
</div>
@endsection