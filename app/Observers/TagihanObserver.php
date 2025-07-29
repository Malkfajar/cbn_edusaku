<?php

namespace App\Observers;

use App\Models\Tagihan;
use App\Notifications\NotifikasiTagihanBaru;
use Illuminate\Support\Facades\Mail;
use App\Mail\TagihanBaruMail;

class TagihanObserver
{
    /**
     * Handle the Tagihan "created" event.
     
      * @param  \App\Models\Tagihan  $tagihan
     * @return void
     */
    public function created(Tagihan $tagihan)
    {
        // Ambil user yang bersangkutan dengan tagihan ini
        $user = $tagihan->user;

        // Pastikan user ada dan bukan admin
        if (!$user || $user->is_admin) {
            return;
        }

        // Siapkan detail notifikasi
        $title = 'Tagihan Baru Telah Diterbitkan';
        $message = "Anda memiliki tagihan baru: '{$tagihan->nama_tagihan}' untuk semester {$tagihan->semester}.";
        $url = route('dashboard'); // Arahkan ke dashboard mahasiswa

        // 1. Kirim Notifikasi In-App (untuk ikon lonceng)
        $user->notifications()->create([
            'title'   => $title,
            'message' => $message,
            'url'     => $url,
        ]);

        // 2. Kirim Notifikasi Push ke Dashboard (jika ada token FCM)
        if ($user->fcm_token) {
            $user->notify(new NotifikasiTagihanBaru($title, $message, $url));
        }
        
        // 3. (Opsional) Kirim Notifikasi via Email menggunakan Queue
        // Mail::to($user->email)->queue(new TagihanBaruMail($user->name, $message, $url));
    }

    /**
     * Handle the Tagihan "updated" event.
     *
     * @param  \App\Models\Tagihan  $tagihan
     * @return void
     */
     /* Handle the Tagihan "updated" event.
     */
    public function updated(Tagihan $tagihan): void
    {
        //
    }

    /**
     * Handle the Tagihan "deleted" event.
     */
    public function deleted(Tagihan $tagihan): void
    {
        //
    }

    /**
     * Handle the Tagihan "restored" event.
     */
    public function restored(Tagihan $tagihan): void
    {
        //
    }

    /**
     * Handle the Tagihan "force deleted" event.
     */
    public function forceDeleted(Tagihan $tagihan): void
    {
        //
    }
}
