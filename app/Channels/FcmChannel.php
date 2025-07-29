<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FcmChannel
{
    public function send($notifiable, Notification $notification)
    {
        // Pastikan pengguna memiliki token FCM
        if (empty($notifiable->fcm_token)) {
            return;
        }

        // Panggil metode 'toFcm' dari kelas notifikasi
        $message = $notification->toFcm($notifiable);

        // Kirim pesan menggunakan Firebase
        Firebase::messaging()->send($message);
    }
}