<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\Transaksi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $summaryData = [
            'total_mahasiswa' => User::where('is_admin', false)->count(),
            'belum_lunas' => Tagihan::whereIn('status', ['belum_dibayar', 'belum_lunas'])->count(),
            'pembayaran_pending' => Transaksi::where('status_transaksi', 'pending')->count(), 
            'pemasukan_bulan_ini' => Transaksi::where('status_transaksi', 'success')
                                              ->whereMonth('updated_at', now()->month)
                                              ->whereYear('updated_at', now()->year)
                                              ->sum(DB::raw('jumlah_bayar + biaya_admin')),
        ];

        // ==================================================================
        // PENYESUAIAN: Query untuk grafik sekarang mengambil dari tabel Transaksi
        // agar biaya admin ikut terhitung dan data menjadi akurat.
        // ==================================================================
        $incomeByMonth = Transaksi::select(
                DB::raw('YEAR(updated_at) as year'),
                DB::raw('MONTH(updated_at) as month'),
                DB::raw('sum(jumlah_bayar + biaya_admin) as total') // Menjumlahkan total pembayaran
            )
            ->where('status_transaksi', 'success') // Berdasarkan transaksi yang sukses
            ->where('updated_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        // ==================================================================
        // AKHIR DARI PENYESUAIAN
        // ==================================================================

        $chartLabels = $incomeByMonth->map(function ($item) {
            return Carbon::createFromDate($item->year, $item->month, 1)->format('M Y');
        });
        
        $chartData = $incomeByMonth->pluck('total');

        return view('admin.dashboard.index', compact(
            'summaryData', 
            'chartLabels',
            'chartData'
        ));
    }
}