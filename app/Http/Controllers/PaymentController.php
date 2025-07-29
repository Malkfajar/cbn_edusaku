<?php

namespace App\Http\Controllers;

use App\Models\BuktiPembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Transaksi;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    /**
     * Menampilkan rincian pembayaran untuk BEBERAPA tagihan yang dipilih.
     */
   // GANTI method showDetails() Anda
public function showDetails(Request $request)
{
    $request->validate(['tagihan' => 'required|array|min:1']);
    $tagihans = Tagihan::whereIn('id', $request->input('tagihan'))->get();
    if ($tagihans->isEmpty()) {
        return redirect()->route('dashboard')->with('error', 'Tagihan tidak ditemukan.');
    }

    // Gunakan sisa_tagihan untuk kalkulasi yang harus dibayar
    $total = $tagihans->sum('sisa_tagihan'); 

     $paymentDetails = [
        'tagihans' => $tagihans,
        'total' => $total,
        'biaya_admin' => env('TRANSACTION_ADMIN_FEE', 0), // Kirim biaya admin ke view
        'terbilang' => ucwords($this->numberToWords($total + env('TRANSACTION_ADMIN_FEE', 0))) . ' Rupiah' // Terbilang di-update
    ];
    return view('payment.details', compact('paymentDetails'));
}

    /**
     * Menampilkan rincian pembayaran untuk SATU tagihan.
     */
    public function showSingleDetail($semester)
    {
        $tagihan = Tagihan::where('user_id', Auth::id())->where('semester', $semester)->firstOrFail();
        $paymentDetails = [
            'tagihans' => collect([$tagihan]),
            'total' => $tagihan->jumlah_tagihan,
            'terbilang' => ucwords($this->numberToWords($tagihan->jumlah_tagihan)) . ' Rupiah'
        ];
        return view('payment.details', compact('paymentDetails'));
    }
    
    /**
     * Memproses pembayaran dan mengubah status tagihan.
     */
// File: app/Http/Controllers/PaymentController.php

public function generateSnapToken(Request $request)
{
    $request->validate([
        'tagihan_ids' => 'required|array|min:1',
        'amount' => 'nullable|numeric|min:1'
    ]);
    
    $tagihans = Tagihan::whereIn('id', $request->input('tagihan_ids'))->get();
    $user = Auth::user();

    $totalSisaTagihan = $tagihans->sum('sisa_tagihan');
    $isInstallmentPayment = ($request->amount && $tagihans->contains('izinkan_cicilan', true));
    
    $amountToPayForBill = $isInstallmentPayment ? $request->amount : $totalSisaTagihan;

    if ($amountToPayForBill > $totalSisaTagihan) {
        return response()->json(['error' => 'Jumlah pembayaran melebihi sisa tagihan.'], 422);
    }

    $adminFee = env('TRANSACTION_ADMIN_FEE', 0);
    $grossAmount = $amountToPayForBill + $adminFee;

    // --- BAGIAN PENTING DIMULAI DI SINI ---
    $item_details = [];
    $description = ''; // Siapkan variabel untuk deskripsi

    if ($isInstallmentPayment) {
        // Membuat deskripsi dinamis untuk cicilan
        $billDescriptions = $tagihans->map(function($tagihan) {
            return $tagihan->nama_tagihan . ' (Smst. ' . $tagihan->semester . ')';
        })->implode(', ');
        $description = 'Cicilan: ' . $billDescriptions;
        
        $item_details[] = ['id' => 'CICILAN', 'price' => $amountToPayForBill, 'quantity' => 1, 'name' => $description];
    } else {
        // Membuat deskripsi dinamis untuk pelunasan
        $description = 'Pelunasan: ' . $tagihans->map(fn($t) => $t->nama_tagihan . ' (Smst. ' . $t->semester . ')')->implode(', ');
        foreach ($tagihans as $tagihan) {
            $item_details[] = ['id' => 'TAGIHAN-'.$tagihan->id, 'price' => $tagihan->sisa_tagihan, 'quantity' => 1, 'name' => 'Tagihan ' . $tagihan->nama_tagihan];
        }
    }
    
    if ($adminFee > 0) {
        $item_details[] = ['id' => 'ADMIN_FEE', 'price' => $adminFee, 'quantity' => 1, 'name' => 'Biaya Administrasi'];
    }

    // Buat transaksi dan SIMPAN DESKRIPSI ke database
    $transaksi = Transaksi::create([
        'tagihan_id' => $tagihans->first()->id,
        'paid_tagihan_ids' => $tagihans->pluck('id'),
        'order_id' => 'TRX-' . $user->id . '-' . time(),
        'jumlah_bayar' => $amountToPayForBill,
        'biaya_admin' => $adminFee,
        'status_transaksi' => 'pending',
        'deskripsi' => $description, // <-- BARIS INI YANG AKAN MENYIMPAN DESKRIPSI
    ]);
    
    // --- AKHIR BAGIAN PENTING ---
    
    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = config('midtrans.is_production');

    $params = [
        'transaction_details' => [
            'order_id' => $transaksi->order_id,
            'gross_amount' => $grossAmount,
        ],
        'customer_details' => [
            'first_name' => $user->name,
            'email' => $user->email,
        ],
        'item_details' => $item_details,
    ];

    try {
        $snapToken = Snap::getSnapToken($params);
        $transaksi->update(['snap_token' => $snapToken]);
        return response()->json(['snap_token' => $snapToken]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    /**
     * Menampilkan halaman riwayat pembayaran.
     */
public function history(Request $request)
{
    $user = Auth::user();

    // Ambil riwayat dari upload manual
    $manualHistories = BuktiPembayaran::where('user_id', $user->id)->where('status', 'Lunas')->get();
    $formattedManualHistories = $manualHistories->map(function ($proof) {
        $tagihan = Tagihan::where('user_id', $proof->user_id)->where('semester', $proof->semester)->first();
        return (object) [
            'created_at' => $proof->created_at,
            'payment_type' => 'Transfer Manual',
            'deskripsi' => $tagihan->nama_tagihan ?? 'Pembayaran Manual',
            'jumlah' => $tagihan->jumlah_total ?? 0,
            'biaya_admin' => 0,
            'file_path' => $proof->file_path,
            'status_tagihan_saat_itu' => 'Lunas', // Dianggap lunas
            'sisa_tagihan_saat_itu' => 0,
        ];
    });

    // Ambil riwayat dari transaksi Midtrans
    $midtransQuery = Transaksi::with('tagihan')
                            ->where('status_transaksi', 'success')
                            ->whereHas('tagihan', function ($query) use ($user) {
                                $query->where('user_id', $user->id);
                            });

    // Filter berdasarkan tagihan_id jika ada di URL
    if($request->has('tagihan_id')){
        $midtransQuery->whereJsonContains('paid_tagihan_ids', intval($request->tagihan_id));
    }

    $midtransHistories = $midtransQuery->latest('updated_at')->get();

    $formattedMidtransHistories = $midtransHistories->map(function ($transaksi) {
        // Hitung status tagihan setelah transaksi ini terjadi
        $tagihanTerkait = $transaksi->tagihan;
        $sisaSetelahBayar = $tagihanTerkait->sisa_tagihan_saat_transaksi($transaksi) ?? 0;

        return (object) [
            'created_at' => $transaksi->updated_at,
            'payment_type' => ucwords(str_replace('_', ' ', $transaksi->payment_method)),
            'deskripsi' => $transaksi->deskripsi,
            'jumlah' => $transaksi->jumlah_bayar,
            'biaya_admin' => $transaksi->biaya_admin,
            'file_path' => null,
            'status_tagihan_saat_itu' => $sisaSetelahBayar <= 0 ? 'Lunas' : 'Belum Lunas',
            'sisa_tagihan_saat_itu' => $sisaSetelahBayar,
        ];
    });

    $histories = $formattedManualHistories->toBase()->merge($formattedMidtransHistories);
    $sortedHistories = $histories->sortByDesc('created_at');

    return view('payment.history', compact('user', 'sortedHistories'));
}

    /**
     * Helper function untuk mengubah angka menjadi kata.
     */
    private function numberToWords($number) {
        $words = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        if ($number < 12) return $words[$number];
        if ($number < 20) return $words[$number - 10] . " belas";
        if ($number < 100) return $words[floor($number / 10)] . " puluh " . $words[$number % 10];
        if ($number < 200) return "seratus " . $this->numberToWords($number - 100);
        if ($number < 1000) return $words[floor($number / 100)] . " ratus " . $this->numberToWords($number % 100);
        if ($number < 2000) return "seribu " . $this->numberToWords($number - 1000);
        if ($number < 1000000) return $this->numberToWords(floor($number / 1000)) . " ribu " . $this->numberToWords($number % 1000);
        if ($number < 1000000000) return $this->numberToWords(floor($number / 1000000)) . " juta " . $this->numberToWords($number % 1000000);
        return "Angka terlalu besar";
    }
}