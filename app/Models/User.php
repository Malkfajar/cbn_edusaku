<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage; 

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'nim',
        'email',
        'program', // Nama baru
        'tahun_masuk',
        'tanggal_lahir',
        'no_telepon',
        'password',
        'profile_photo_path',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean', // Casting is_admin sebagai boolean
            'tanggal_lahir' => 'date', // Casting tanggal_lahir sebagai date
        ];
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function notifications()
{
    return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
}

 public function getPhotoUrlAttribute()
{
    // UBAH BAGIAN INI: dari $this->photo menjadi $this->profile_photo_path
    if ($this->profile_photo_path && Storage::disk('public')->exists($this->profile_photo_path)) {
        return Storage::url($this->profile_photo_path);
    }

    // Return default image if no photo is set
    return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
}

}
