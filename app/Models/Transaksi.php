<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Memudahkan mass assignment
protected $casts = [
    'paid_tagihan_ids' => 'array',
];
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }
}
