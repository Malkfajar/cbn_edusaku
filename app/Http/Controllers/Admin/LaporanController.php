<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\LaporanKeuanganExport;
use Maatwebsite\Excel\Facades\Excel; 
use App\Models\Transaksi;

class LaporanController extends Controller
{
public function index(Request $request)
{
    // 1. Tentukan rentang tanggal
    $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
    $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfMonth();

    // 2. Ambil data transaksi sukses untuk perhitungan
    $successfulTransactions = Transaksi::where('status_transaksi', 'success')
                                     ->whereBetween('updated_at', [$startDate, $endDate]);

    // A. Hitung total pemasukan dari pembayaran tagihan saja
    $pemasukanDariTagihan = (clone $successfulTransactions)->sum('jumlah_bayar');
    // B. Hitung total pemasukan dari biaya admin saja
    $pemasukanDariBiayaAdmin = (clone $successfulTransactions)->sum('biaya_admin');
    // C. Total Pemasukan adalah gabungan keduanya
    $totalPemasukan = $pemasukanDariTagihan + $pemasukanDariBiayaAdmin;
    
    // D. Hitung total sisa dari tagihan yang belum lunas
    $totalBelumLunas = Tagihan::where('status', 'belum_lunas')
                              ->whereBetween('created_at', [$startDate, $endDate])
                              ->sum('sisa_tagihan');
                              
    // E. Hitung total tagihan yang belum dibayar sama sekali
    $totalBelumDibayarDanPending = Tagihan::where('status', 'belum_dibayar')
                                        ->whereBetween('created_at', [$startDate, $endDate])
                                        ->sum('sisa_tagihan');

    // 3. Ambil data untuk tabel rincian
    $transactions = Transaksi::with('tagihan.user')
                           ->where('status_transaksi', 'success')
                           ->whereBetween('updated_at', [$startDate, $endDate])
                           ->latest('updated_at')
                           ->paginate(10);
    
    // 4. Siapkan data untuk grafik
    $incomeByDay = Transaksi::select(
            DB::raw('DATE(updated_at) as date'),
            DB::raw('sum(jumlah_bayar + biaya_admin) as total')
        )
        ->where('status_transaksi', 'success')
        ->whereBetween('updated_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

    $chartLabels = $incomeByDay->pluck('date')->map(function($date) {
        return Carbon::parse($date)->format('d M');
    });
    $chartData = $incomeByDay->pluck('total');

    // --- PERBAIKAN DI SINI ---
    // Menambahkan variabel yang hilang ke compact()
    return view('admin.laporan.index', compact(
        'totalPemasukan', 
        'pemasukanDariTagihan',
        'pemasukanDariBiayaAdmin',
        'totalBelumLunas', // Variabel ini ditambahkan
        'totalBelumDibayarDanPending', // Variabel ini ditambahkan
        'transactions', 
        'chartLabels', 
        'chartData',
        'startDate',
        'endDate'
    ));
}


    public function export(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfMonth();
        $fileName = 'Laporan_Keuangan_' . $startDate->format('d-m-Y') . '_-_' . $endDate->format('d-m-Y') . '.xlsx';
        
        return Excel::download(new LaporanKeuanganExport($startDate, $endDate), $fileName);
    }
}