@extends('layouts.dashboard')

@section('title', 'Riwayat Pembayaran')

@section('content')
    <div x-data class="bg-white rounded-lg shadow-md p-6 md:p-8 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-[#0b3d91] mb-6">Riwayat Pembayaran</h2>
        
        @if($sortedHistories->count() > 0)
            <div class="space-y-6">
                @foreach($sortedHistories as $history)
                <div x-data="{ show: false }" x-init="setTimeout(() => { show = true }, {{ $loop->index * 100 }})" x-show="show" x-transition:enter="transition ease-out duration-500 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" 
                     class="p-6 rounded-lg flex flex-col sm:flex-row justify-between items-start gap-4 transition-all duration-300 ease-in-out hover:shadow-xl hover:scale-[1.02] {{ $history->status_tagihan_saat_itu == 'Lunas' ? 'bg-green-50' : 'bg-gray-100' }}">  
                    
                    <div class="space-y-2 flex-grow">
                        {{-- KETERANGAN PEMBAYARAN DINAMIS --}}
                        <p class="font-bold text-gray-800 text-base">{{ $history->deskripsi ?? 'Pembayaran Tagihan' }}</p>

                        <div class="text-sm">
                            <span class="text-gray-500">Tanggal:</span>
                            <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($history->created_at)->format('d F Y, H:i') }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-gray-500">Metode:</span>
                            <span class="font-semibold text-gray-700">{{ $history->payment_type }}</span>
                        </div>
                        
                        {{-- RINCIAN BIAYA --}}
                        <div class="pt-2">
                            <span class="text-sm text-gray-500">Rincian:</span>
                            <ul class="list-disc list-inside text-sm mt-1">
                                <li>Pembayaran Tagihan: <span class="font-semibold">Rp {{ number_format($history->jumlah, 0, ',', '.') }}</span></li>
                                @if($history->biaya_admin > 0)
                                <li>Biaya Administrasi: <span class="font-semibold">Rp {{ number_format($history->biaya_admin, 0, ',', '.') }}</span></li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    {{-- STATUS TAGIHAN SETELAH TRANSAKSI --}}
                    <div class="flex-shrink-0 text-right">
                        <p class="px-3 py-1 text-sm font-semibold rounded-full {{ $history->status_tagihan_saat_itu == 'Lunas' ? 'text-green-800 bg-green-200' : 'text-orange-800 bg-orange-200' }}">
                            Status Tagihan: {{ $history->status_tagihan_saat_itu }}
                        </p>
                        @if($history->status_tagihan_saat_itu == 'Belum Lunas')
                        <p class="text-xs text-gray-600 mt-2">
                            Sisa: <span class="font-bold">Rp {{ number_format($history->sisa_tagihan_saat_itu, 0, ',', '.') }}</span>
                        </p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div x-data="{ show: false }" x-init="show = true" x-show="show" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="text-center py-10">
                <p class="text-gray-500">Anda belum memiliki riwayat pembayaran.</p>
            </div>
        @endif
    </div>
@endsection