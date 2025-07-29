<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use App\Channels\FcmChannel; // Kita akan buat ini sebentar lagi

class PembayaranDiverifikasi extends Notification
{
    use Queueable;

    protected $title;
    protected $body;
    protected $url;

    public function __construct($title, $body, $url)
    {
        $this->title = $title;
        $this->body = $body;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return [FcmChannel::class]; // Menggunakan channel custom kita
    }

    public function toFcm($notifiable)
    {
        return CloudMessage::withTarget('token', $notifiable->fcm_token)
            ->withNotification(FirebaseNotification::create($this->title, $this->body))
            ->withData(['url' => $this->url]); // Mengirim URL untuk di-klik
    }
}