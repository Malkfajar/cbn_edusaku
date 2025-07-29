<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification as MidtransNotification;
use App\Models\Tagihan;
use App\Models\Notification;

class MidtransController extends Controller
{
   
public function notificationHandler(Request $request)
    {
        // Konfigurasi server key
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            // Buat instance notifikasi dari Midtrans
            $notification = new MidtransNotification();
    
            // Ambil status transaksi & order_id
            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;
    
            // Cari transaksi di database Anda, eager load relasi untuk efisiensi
            $transaksi = Transaksi::with('tagihan.user')->where('order_id', $orderId)->first();
    
            // Hanya proses jika transaksi ditemukan dan statusnya masih 'pending'
            if ($transaksi && $transaksi->status_transaksi == 'pending') {
                
                // Jika pembayaran sukses
                if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                    // 1. Update status transaksi lokal menjadi 'success'
                    $transaksi->update([
                        'status_transaksi' => 'success',
                        'payment_method' => $notification->payment_type
                    ]);
                    
                    // 2. Update setiap tagihan yang terkait
                    $paidTagihanIds = $transaksi->paid_tagihan_ids;
                    if (!empty($paidTagihanIds)) {
                        $allTagihan = \App\Models\Tagihan::whereIn('id', $paidTagihanIds)->get();
                        foreach ($allTagihan as $tagihan) {
                            if (count($paidTagihanIds) === 1) { // Pembayaran tunggal (bisa cicilan)
                                $newSisaTagihan = $tagihan->sisa_tagihan - $transaksi->jumlah_bayar;
                                $tagihan->update([
                                    'sisa_tagihan' => $newSisaTagihan,
                                    'status' => $newSisaTagihan <= 0 ? 'lunas' : 'belum_lunas'
                                ]);
                            } else { // Pembayaran multi-tagihan dianggap lunas
                                 $tagihan->update(['sisa_tagihan' => 0, 'status' => 'lunas']);
                            }
                        }
                    }

                    // ==================================================================
                    // DITAMBAHKAN: BUAT NOTIFIKASI UNTUK MAHASISWA
                    // ==================================================================
                    $user = $transaksi->tagihan->user; // Dapatkan user dari relasi
                    if ($user) {
                        Notification::create([
                            'user_id' => $user->id,
                            'title'   => 'Pembayaran Berhasil',
                            'message' => 'Pembayaran Anda untuk "' . $transaksi->deskripsi . '" telah berhasil kami terima.',
                            'url'     => route('payment.history'), // Arahkan ke halaman riwayat
                        ]);
                    }
                    // ==================================================================
                    // AKHIR BLOK BARU
                    // ==================================================================

                // Jika pembayaran gagal
                } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                    $transaksi->update(['status_transaksi' => 'failed']);
                }
            }
    
            return response()->json(['message' => 'Notification handled'], 200);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Midtrans Notification Error: " . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
}