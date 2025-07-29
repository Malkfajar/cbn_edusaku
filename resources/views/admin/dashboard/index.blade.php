@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

{{-- Penambahan: Script untuk animasi counter angka --}}
<script>
    function counter(target) {
        return {
            current: 0,
            target: target,
            init() {
                const observer = new IntersectionObserver(entries => {
                    if (entries[0].isIntersecting) {
                        const duration = 1500; // durasi animasi dalam ms
                        const stepTime = Math.abs(Math.floor(duration / this.target));
                        let startTime = null;

                        const animate = (timestamp) => {
                            if (!startTime) startTime = timestamp;
                            const progress = timestamp - startTime;
                            const newCurrent = Math.min((progress / duration) * this.target, this.target);
                            this.current = newCurrent;

                            if (progress < duration) {
                                requestAnimationFrame(animate);
                            } else {
                                this.current = this.target;
                            }
                        };
                        requestAnimationFrame(animate);
                        observer.disconnect(); // Hentikan observer setelah animasi dimulai
                    }
                }, { threshold: 0.1 });
                observer.observe(this.$el);
            }
        }
    }
</script>

{{-- Penambahan: Alpine.js untuk mengontrol animasi saat halaman dimuat --}}
<div x-data="{ loaded: false }" x-init="requestAnimationFrame(() => { loaded = true })">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {{-- Kartu 1: Total Mahasiswa --}}
        {{-- Penambahan: Animasi hover (angkat & skala), animasi load-in bertahap, dan class 'group' --}}
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between group transition-all duration-300 ease-out hover:scale-105 hover:-translate-y-1" 
             :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" style="transition-delay: 100ms">
            {{-- Penambahan: x-data untuk animasi counter --}}
            <div x-data="counter({{ $summaryData['total_mahasiswa'] }})">
                <p class="text-sm text-gray-500">Total Mahasiswa</p>
                <p class="text-3xl font-bold text-gray-800" x-text="Math.floor(current).toLocaleString('id-ID')"></p>
            </div>
            {{-- Penambahan: Animasi ikon saat hover --}}
            <div class="bg-blue-100 p-3 rounded-full transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
        
        {{-- Kartu 2: Tagihan Belum Lunas --}}
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between group transition-all duration-300 ease-out hover:scale-105 hover:-translate-y-1"
             :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" style="transition-delay: 200ms">
            <div x-data="counter({{ $summaryData['belum_lunas'] }})">
                <p class="text-sm text-gray-500">Tagihan Belum Lunas</p>
                <p class="text-3xl font-bold text-gray-800" x-text="Math.floor(current).toLocaleString('id-ID')"></p>
            </div>
            <div class="bg-orange-100 p-3 rounded-full transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between group transition-all duration-300 ease-out hover:scale-105 hover:-translate-y-1" :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" style="transition-delay: 300ms">
            <div x-data="counter({{ $summaryData['pembayaran_pending'] }})"> 
                <p class="text-sm text-gray-500">Pembayaran Pending</p>
                <p class="text-3xl font-bold text-gray-800" x-text="Math.floor(current).toLocaleString('id-ID')"></p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        {{-- Kartu 4: Pemasukan Bulan Ini --}}
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between group transition-all duration-300 ease-out hover:scale-105 hover:-translate-y-1" :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" style="transition-delay: 400ms">
            <div x-data="counter({{ $summaryData['pemasukan_bulan_ini'] }})">
                <p class="text-sm text-gray-500">Pemasukan Bulan Ini</p>
                <p class="text-3xl font-bold text-gray-800" x-text="'Rp' + (current / 1000000).toLocaleString('id-ID', {minimumFractionDigits: 1, maximumFractionDigits: 1}) + ' jt'"></p>
            </div>
            <div class="bg-green-100 p-3 rounded-full transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6">
                 <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Penambahan: Animasi load-in untuk container grafik --}}
        <div class="lg:col-span-3 bg-white p-6 rounded-lg shadow-md transition-all duration-500 ease-out"
             :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'" style="transition-delay: 500ms">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Pemasukan (6 Bulan Terakhir)</h3>
            <div class="relative h-64 md:h-80">
                <canvas id="incomeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tunda inisialisasi chart agar tidak mengganggu animasi layout
        setTimeout(() => {
            const ctx = document.getElementById('incomeChart').getContext('2d');
            
            const chartLabels = @json($chartLabels);
            const chartData = @json($chartData);

            const incomeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Pemasukan', 
                        data: chartData,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        // Penambahan: Animasi pada bar chart
                        borderRadius: 4,
                        barThickness: 'flex',
                        maxBarThickness: 30,
                    }]
                },
                options: {
                    // Penambahan: Animasi saat chart dimuat
                    animation: {
                        duration: 1000,
                        easing: 'easeOutCubic',
                        onComplete: () => { /* Animasi selesai */ }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 2000000, 
                                callback: function(value, index, values) {
                                    if (value >= 0) {
                                        return (value / 1000000) + ' jt';
                                    }
                                    return value;
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
                                        const valueInMillions = (context.parsed.y / 1000000).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 1 });
                                        label += `Rp ${valueInMillions} jt`;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }, 500); // Penundaan 500ms
    });
</script>
@endsection