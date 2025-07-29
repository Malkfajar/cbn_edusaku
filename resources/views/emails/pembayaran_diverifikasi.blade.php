<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Verifikasi Pembayaran</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 90%; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 1px solid #ddd; }
        .content { padding: 20px 0; }
        .footer { text-align: center; font-size: 0.9em; color: #777; padding-top: 20px; border-top: 1px solid #ddd; }
        .button { display: inline-block; padding: 10px 20px; margin-top: 20px; background-color: #0b3d91; color: #fff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Notifikasi Pembayaran</h2>
        </div>
        <div class="content">
            <p>Halo, <strong>{{ $userName }}</strong>,</p>
            <p>{{ $notificationMessage }}</p>
            <p>
                Anda dapat melihat rincian riwayat pembayaran Anda dengan mengklik tombol di bawah ini.
            </p>
            <a href="{{ $notificationUrl }}" class="button">Lihat Riwayat Pembayaran</a>
            <p>Jika Anda tidak merasa melakukan transaksi ini, harap abaikan email ini.</p>
            <p>Terima kasih.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Semua Hak Cipta Dilindungi.</p>
        </div>
    </div>
</body>
</html>