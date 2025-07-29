@extends('admin.layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')

{{-- Menambahkan Style Kustom untuk Animasi --}}
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Kelas untuk animasi fade-in-up */
    .animate-fadeInUp {
        opacity: 0; /* Mulai dari transparan agar tidak flicker */
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    /* Kelas untuk animasi baris tabel */
    .table-row-animated {
        opacity: 0;
        animation: fadeInUp 0.5s ease-out forwards;
    }
</style>

<div class="space-y-6">
    {{-- 1. Blok Filter --}}
    <div class="bg-white p-6 rounded-lg shadow-md animate-fadeInUp">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Laporan Keuangan</h2>
                <p class="text-sm text-gray-500 mt-1">Analisis pemasukan dan tagihan berdasarkan rentang waktu.</p>
            </div>
            <div class="mt-4 sm:mt-0 w-full sm:w-auto">
                <form action="{{ route('admin.laporan.index') }}" method="GET" class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <div class="flex items-center gap-2 w-full">
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="text-gray-500">-</span>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto mt-2 sm:mt-0">
                        {{-- Animasi hover pada tombol --}}
                        <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm transition-transform transform hover:scale-105">Filter</button>
                        <a href="{{ route('admin.laporan.export', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
                           class="w-full sm:w-auto flex justify-center bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 text-sm transition-transform transform hover:scale-105" title="Ekspor ke Excel">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. Blok Ringkasan (Summary Cards) --}}
    {{-- Diubah: Menggunakan grid 3 kolom pada layar medium ke atas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fadeInUp" style="animation-delay: 150ms;">
        {{-- Kartu Pemasukan --}}
        <div class="bg-white p-6 rounded-lg shadow-md transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <p class="text-sm text-gray-500">Total Pemasukan (Lunas)</p>
            <div x-data="{ count: 0 }" x-init="
                let target = {{ $totalPemasukan }};
                let duration = 1500;
                if (target === 0) { count = 0; return; }
                let stepTime = 16;
                let totalSteps = duration / stepTime;
                let increment = target / totalSteps;
                let interval = setInterval(() => {
                    if (count < target) {
                        count += increment;
                    } else {
                        count = target;
                        clearInterval(interval);
                    }
                }, stepTime);
            ">
                <p class="text-3xl font-bold text-green-600 mt-2">Rp<span x-text="Math.round(count).toLocaleString('id-ID')"></span></p>
            </div>
            <p class="text-xs text-gray-400 mt-1">Dalam rentang tanggal yang dipilih</p>
        </div>
        
        {{-- Diubah: Kartu Tagihan Belum Lunas --}}
        <div class="bg-white p-6 rounded-lg shadow-md transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <p class="text-sm text-gray-500">Total Tagihan Belum Lunas</p>
             <div x-data="{ count: 0 }" x-init="
                let target = {{ $totalBelumLunas }};
                let duration = 1500;
                if (target === 0) { count = 0; return; }
                let stepTime = 16;
                let totalSteps = duration / stepTime;
                let increment = target / totalSteps;
                let interval = setInterval(() => {
                    if (count < target) {
                        count += increment;
                    } else {
                        count = target;
                        clearInterval(interval);
                    }
                }, stepTime);
            ">
                <p class="text-3xl font-bold text-orange-500 mt-2">Rp<span x-text="Math.round(count).toLocaleString('id-ID')"></span></p>
            </div>
            <p class="text-xs text-gray-400 mt-1">Total tagihan yang sudah diangsur</p>
        </div>

        {{-- Ditambahkan: Kartu Tagihan Belum Dibayar & Pending --}}
        <div class="bg-white p-6 rounded-lg shadow-md transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
            <p class="text-sm text-gray-500">Total Tagihan Belum Dibayar & Pending</p>
             <div x-data="{ count: 0 }" x-init="
                let target = {{ $totalBelumDibayarDanPending }};
                let duration = 1500;
                if (target === 0) { count = 0; return; }
                let stepTime = 16;
                let totalSteps = duration / stepTime;
                let increment = target / totalSteps;
                let interval = setInterval(() => {
                    if (count < target) {
                        count += increment;
                    } else {
                        count = target;
                        clearInterval(interval);
                    }
                }, stepTime);
            ">
                <p class="text-3xl font-bold text-yellow-600 mt-2">Rp<span x-text="Math.round(count).toLocaleString('id-ID')"></span></p>
            </div>
            <p class="text-xs text-gray-400 mt-1">Tagihan yang menunggu pembayaran</p>
        </div>
    </div>

    {{-- 3. Blok Grafik --}}
    <div class="bg-white p-6 rounded-lg shadow-md animate-fadeInUp" style="animation-delay: 300ms;">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Pemasukan Harian</h3>
        <div class="relative h-96">
            <canvas id="laporanChart"></canvas>
        </div>
    </div>

    {{-- 4. Blok Tabel Rincian --}}
    <div class="bg-white p-6 rounded-lg shadow-md animate-fadeInUp" style="animation-delay: 450ms;">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Rincian Transaksi Lunas</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
    <tr>
        <th class="px-6 py-3">Tanggal Lunas</th>
        <th class="px-6 py-3">Mahasiswa</th>
        <th class="px-6 py-3">Keterangan</th>
        <th class="px-6 py-3 text-center">Tipe Pembayaran</th> {{-- KOLOM BARU --}}
        <th class="px-6 py-3 text-right">Jumlah</th>
    </tr>
</thead>
               {{-- GANTI SELURUH ISI TBODY ANDA DENGAN INI --}}
{{-- GANTI SELURUH ISI TBODY ANDA DENGAN INI --}}
<tbody>
    @forelse($transactions as $trx)
    <tr class="bg-white border-b hover:bg-gray-50 table-row-animated" style="animation-delay: {{ $loop->index * 70 }}ms">
        <td class="px-6 py-4 whitespace-nowrap">{{ $trx->updated_at->format('d M Y, H:i') }}</td>
        <td class="px-6 py-4 font-medium text-gray-900">{{ $trx->tagihan->user->name ?? 'User Dihapus' }}</td>
        
        {{-- Menggunakan kolom deskripsi yang sudah kita simpan --}}
        <td class="px-6 py-4 text-sm text-gray-600">{{ $trx->deskripsi ?? 'N/A' }}</td>
        
        {{-- KOLOM BARU: Menampilkan tipe pembayaran secara dinamis --}}
        <td class="px-6 py-4 text-center">
            @if(Str::startsWith($trx->deskripsi, 'Cicilan'))
                <span class="px-3 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full">
                    Cicilan
                </span>
            @else
                <span class="px-3 py-1 text-xs font-semibold text-indigo-800 bg-indigo-100 rounded-full">
                    Pelunasan
                </span>
            @endif
        </td>

        <td class="px-6 py-4 text-right font-medium">Rp{{ number_format($trx->jumlah_bayar + $trx->biaya_admin, 0, ',', '.') }}</td>
    </tr>
    @empty
    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 animate-fadeInUp">Tidak ada transaksi lunas pada rentang tanggal ini.</td></tr>
    @endforelse
</tbody>
                {{-- AWAL KODE TAMBAHAN --}}
              {{-- GANTI SELURUH BLOK TFOOT ANDA DENGAN INI --}}
<tfoot class="bg-gray-100 font-semibold">
    <tr class="text-gray-900">
        {{-- DIUBAH: colspan menjadi "4" agar sejajar dengan 5 kolom --}}
        <td colspan="4" class="px-6 py-4 text-right">Total Tagihan</td>
        <td class="px-6 py-4 text-right">Rp{{ number_format($totalPemasukan, 0, ',', '.') }}</td>
    </tr>
</tfoot>
            </table>
        </div>
        <div class="mt-6">{{ $transactions->appends(request()->query())->links() }}</div>
    </div>
</div>

{{-- Memastikan Alpine.js dimuat untuk animasi count-up. Sebaiknya diletakkan di layout utama (app.blade.php) --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('laporanChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pemasukan Harian',
                data: @json($chartData),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 4, // Menambahkan sudut melengkung pada bar chart
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            // Menambahkan animasi bawaan Chart.js yang lebih menarik
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart',
                delay: (context) => {
                    let delay = 0;
                    if (context.type === 'data' && context.mode === 'default') {
                        delay = context.dataIndex * 50; // Delay setiap bar
                    }
                    return delay;
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000) + ' jt';
                            }
                            if (value >= 1000) {
                                return 'Rp ' + (value / 1000) + ' rb';
                            }
                            return 'Rp ' + value;
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection