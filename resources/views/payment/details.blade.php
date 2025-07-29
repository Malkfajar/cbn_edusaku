@extends('layouts.dashboard')

@section('title', 'Rincian Pembayaran')

@section('content')

{{-- Blok PHP untuk mendefinisikan variabel yang dibutuhkan oleh view --}}
@php
    // Cek apakah ada tagihan yang diizinkan untuk dicicil dalam pembayaran ini
    $isInstallmentAllowed = $paymentDetails['tagihans']->contains('izinkan_cicilan', true);
@endphp

<div class="animate-fade-in-up">
    <div class="bg-white rounded-lg shadow-md p-6 md:p-8 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-[#0b3d91] text-center mb-6">Konfirmasi Pembayaran</h2>
        
        <div class="bg-gray-100 p-6 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-base">
                <div><p class="text-sm text-gray-500">Nama</p><p class="font-semibold text-gray-800">{{ auth()->user()->name }}</p></div>
                <div><p class="text-sm text-gray-500">NIM</p><p class="font-semibold text-gray-800">{{ auth()->user()->nim }}</p></div>
                <div><p class="text-sm text-gray-500">Tagihan Untuk</p><p class="font-semibold text-gray-800">
                    @foreach($paymentDetails['tagihans'] as $tagihan)
                        {{ $tagihan->nama_tagihan }} (Smst. {{ $tagihan->semester }}){{ !$loop->last ? ',' : '' }}
                    @endforeach
                </p></div>
            </div>
        </div>

        <div class="mt-6 max-w-md mx-auto space-y-3">
    {{-- Rincian Tagihan --}}
    <div class="flex justify-between text-base">
        <span class="text-gray-600">Total Tagihan:</span>
        <span class="font-semibold text-gray-800">Rp {{ number_format($paymentDetails['total'], 0, ',', '.') }}</span>
    </div>

    {{-- Rincian Biaya Admin --}}
    <div class="flex justify-between text-base">
        <span class="text-gray-600">Biaya Administrasi:</span>
        <span class="font-semibold text-gray-800">Rp {{ number_format($paymentDetails['biaya_admin'], 0, ',', '.') }}</span>
    </div>

    {{-- Garis Pemisah --}}
    <hr class="border-t border-gray-200">

    {{-- Total Pembayaran --}}
    <div class="flex justify-between items-center text-lg pt-2">
        <span class="font-bold text-[#0b3d91]">Total Pembayaran:</span>
        <span class="text-2xl font-bold text-[#0b3d91]">Rp {{ number_format($paymentDetails['total'] + $paymentDetails['biaya_admin'], 0, ',', '.') }}</span>
    </div>

    <p class="text-gray-500 mt-1 italic text-center text-sm">{{ $paymentDetails['terbilang'] }}</p>
</div>

        {{-- Kolom input ini hanya akan muncul jika cicilan diizinkan --}}
        @if($isInstallmentAllowed)
        <div class="mt-6 max-w-sm mx-auto">
            <label for="amount_to_pay" class="block text-sm font-medium text-gray-700 text-center">Jumlah yang akan dibayar (cicilan)</label>
            <input type="text" id="amount_to_pay_display" 
                   @input="formatRupiah($event, 'amount_to_pay_hidden')"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-lg text-center"
                   placeholder="Contoh: Rp 3.000.000">
            <input type="hidden" name="amount_to_pay" id="amount_to_pay_hidden">
        </div>
        @endif

        <div class="mt-8 flex justify-center">
            <button id="pay-button" class="w-full sm:w-auto px-8 py-3 bg-[#0b3d91] text-white font-semibold rounded-lg shadow-md hover:bg-[#1e4e9c] transition-all">
                Bayar Sekarang
            </button>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script type="text/javascript">

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
    
document.getElementById('pay-button').onclick = function(){
    this.disabled = true;
    this.innerHTML = 'Memproses...';

    const amountInput = document.getElementById('amount_to_pay_hidden');
    const customAmount = amountInput ? amountInput.value : null;

    fetch('{{ route("payment.generate_token") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            tagihan_ids: [{{ $paymentDetails['tagihans']->pluck('id')->implode(',') }}],
            amount: customAmount 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.snap_token) {
            snap.pay(data.snap_token, {
                onSuccess: function(result){
                    alert("Pembayaran berhasil! Halaman akan dimuat ulang.");
                    window.location.href = '{{ route("dashboard") }}';
                },
                onPending: function(result){
                    alert("Pembayaran Anda sedang diproses. Silakan selesaikan pembayaran.");
                    window.location.href = '{{ route("dashboard") }}';
                },
                onError: function(result){
                    alert("Pembayaran gagal!");
                },
                onClose: function(){
                    console.log('Anda menutup pop-up tanpa menyelesaikan pembayaran.');
                }
            });
        } else {
            alert('Gagal memulai pembayaran: ' + (data.error || 'Unknown error'));
        }
        // Kembalikan tombol ke normal jika ada error atau pop-up ditutup
        document.getElementById('pay-button').disabled = false;
        document.getElementById('pay-button').innerHTML = 'Bayar Sekarang';
    });
};
</script>
@endsection