<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Tidak perlu import model Tagihan di sini jika kita pakai relasi

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard mahasiswa.
     */
    public function index()
    {
        $user = Auth::user();

        // PERUBAHAN UTAMA:
        // Mengambil semua data tagihan milik pengguna yang sedang login
        // melalui relasi 'tagihans' yang sudah kita buat di model User.
        // Data diurutkan berdasarkan semester.
        $tagihans = $user->tagihans()->orderBy('semester', 'asc')->get();

        // Kirim data user dan tagihannya ke view
        return view('dashboard.index', compact('user', 'tagihans'));
    }
}
