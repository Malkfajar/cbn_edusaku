<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

      public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
   protected $fillable = [
        'user_id',
        'semester',
        'nama_tagihan',
        'jumlah_total',   // Ganti dari jumlah_tagihan
        'sisa_tagihan',   // Tambahkan ini
        'status',
        'izinkan_cicilan' // Tambahkan ini
    ];


    /**
     * Mendefinisikan relasi "milik" ke model User.
     * Satu tagihan hanya dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sisa_tagihan_saat_transaksi(Transaksi $transaksi)
{
    // Ambil semua transaksi sukses untuk tagihan ini HINGGA transaksi saat ini
    $pembayaranSebelumnya = $this->transaksis()
                                ->where('status_transaksi', 'success')
                                ->where('updated_at', '<=', $transaksi->updated_at)
                                ->sum('jumlah_bayar');

    return $this->jumlah_total - $pembayaranSebelumnya;
}
}
