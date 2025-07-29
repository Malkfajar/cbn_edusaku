    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Auth\LoginController;
    use App\Http\Controllers\DashboardController;
    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\PaymentController;
    use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
    use App\Http\Controllers\Admin\MahasiswaController;
    use App\Http\Controllers\Admin\TagihanController;
    use App\Http\Controllers\Admin\LaporanController;
    use App\Http\Controllers\Api\NotificationController; 
    use App\Http\Controllers\MidtransController;
   

    Auth::routes(['register' => false]);


    Route::get('/', function () {
        return view('auth.login');
    });

    // --- AREA MAHASISWA ---
    // Semua rute mahasiswa untuk CBN EDUSAKU
    Route::middleware(['auth', 'is_mahasiswa'])->group(function() {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::post('/pembayaran/rincian', [PaymentController::class, 'showDetails'])->name('payment.showDetails');
        Route::get('/pembayaran/rincian/{semester}', [PaymentController::class, 'showSingleDetail'])->name('payment.showSingleDetail');
        Route::post('/pembayaran/bukti', [PaymentController::class, 'uploadProof'])->name('payment.uploadProof');
        Route::get('/pembayaran/riwayat', [PaymentController::class, 'history'])->name('payment.history');
        //Route::post('/pembayaran/process', [PaymentController::class, 'processPayment'])->name('payment.process');
        Route::post('/profile/fcm-token', [ProfileController::class, 'updateFcmToken'])->name('profile.fcm-token.update'); 
           // RUTE API UNTUK NOTIFIKASI (BARU)
        Route::get('/api/notifications', [NotificationController::class, 'index'])->name('api.notifications.index');
        Route::get('/api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.notifications.unread-count');
        Route::post('/api/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-as-read');
        Route::post('/pembayaran/generate-token', [PaymentController::class, 'generateSnapToken'])->name('payment.generate_token');
    });


    // --- AREA ADMIN ---
    // Semua rute admin untuk CBN EDUSAKU
    Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/summary', [AdminDashboardController::class, 'getSummaryData'])->name('dashboard.summary');
        Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
        Route::put('/mahasiswa/{user}', [MahasiswaController::class, 'update'])->name('mahasiswa.update');
        Route::post('/mahasiswa', [MahasiswaController::class, 'store'])->name('mahasiswa.store');
        Route::delete('/mahasiswa/{user}', [MahasiswaController::class, 'destroy'])->name('mahasiswa.destroy');

        Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
        Route::post('/tagihan', [TagihanController::class, 'store'])->name('tagihan.store');
        Route::put('/tagihan/{tagihan}', [TagihanController::class, 'update'])->name('tagihan.update');
        Route::delete('/tagihan/{tagihan}', [TagihanController::class, 'destroy'])->name('tagihan.destroy');
        Route::post('/tagihan/bulk', [TagihanController::class, 'storeBulk'])->name('tagihan.storeBulk');
        Route::post('/tagihan/import', [TagihanController::class, 'import'])->name('tagihan.import');
        Route::post('/tagihan/{tagihan}/toggle-cicilan', [TagihanController::class, 'toggleInstallment'])->name('tagihan.toggleInstallment');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');

        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::post('/profile/photo', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
        
    });

Route::get('lupa-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('lupa-password', [LoginController::class, 'verifyIdentifier'])->name('password.verify');
Route::get('reset-password/{identifier}', [LoginController::class, 'showResetForm'])->name('password.reset');
// URL untuk update sekarang menyertakan {identifier} dan menunjuk ke metode baru
Route::post('reset-password/{identifier}', [LoginController::class, 'performPasswordUpdate'])->name('password.perform_update');
Route::post('/midtrans/notification', [MidtransController::class, 'notificationHandler'])->name('midtrans.notification');