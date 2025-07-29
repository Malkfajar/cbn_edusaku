<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // --- Tidak ada perubahan di bagian ini ---
    protected function authenticated(Request $request, $user)
    {
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    }

    public function __construct() { $this->middleware('guest')->except('logout'); }
    public function username() { return 'identifier'; }
    protected function loggedOut(Request $request) { return redirect('/login'); }

    protected function attemptLogin(Request $request)
    {
        $identifier = $request->input('identifier');
        $password = $request->input('password');
        if ($this->guard()->attempt(['username' => $identifier, 'password' => $password], $request->filled('remember'))) {
            return true;
        }
        if ($this->guard()->attempt(['nim' => $identifier, 'password' => $password], $request->filled('remember'))) {
            return true;
        }
        return false;
    }
    // --- Akhir bagian yang tidak berubah ---


    /**
     * LANGKAH 1: Menampilkan form lupa password.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.passwords.forgot');
    }

    /**
     * LANGKAH 2: Verifikasi NIM/Username dan redirect ke URL reset dengan identifier.
     */
    public function verifyIdentifier(Request $request)
    {
        $request->validate(['identifier' => 'required|string']);
        $user = User::where('nim', $request->identifier)->orWhere('username', $request->identifier)->first();

        if ($user) {
            // Redirect ke route 'password.reset' dengan membawa identifier di URL
            return redirect()->route('password.reset', ['identifier' => $request->identifier]);
        }
        return back()->withErrors(['identifier' => 'NIM atau Username tidak ditemukan.']);
    }

    /**
     * LANGKAH 3: Menampilkan form reset password, mengambil identifier dari URL.
     */
    public function showResetForm($identifier)
    {
        // Cek lagi apakah user dengan identifier ini ada
        $userExists = User::where('nim', $identifier)->orWhere('username', $identifier)->exists();
        if (!$userExists) {
            return redirect()->route('password.request')->withErrors(['identifier' => 'Akun tidak valid atau tidak ditemukan.']);
        }
        // Kirim identifier ke view
        return view('auth.passwords.reset', ['identifier' => $identifier]);
    }

    /**
     * LANGKAH 4 (BARU): Memproses update password, mengambil identifier dari URL.
     */
    public function performPasswordUpdate(Request $request, $identifier)
    {
        // Validasi hanya untuk password, karena identifier sudah ada dari URL
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('nim', $identifier)->orWhere('username', 'like', $identifier)->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
        }

        return redirect()->route('password.request')->withErrors(['identifier' => 'Gagal memperbarui password, akun tidak ditemukan.']);
    }
}