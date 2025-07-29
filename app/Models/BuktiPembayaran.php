<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiPembayaran extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'semester',
        'file_path',
        'notes',
        'jumlah', 
        'status',
    ];

    /**
     * Get the user that owns the payment proof.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}