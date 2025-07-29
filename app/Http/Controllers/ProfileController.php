<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // <-- Tambahkan ini
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password; // <-- Tambahkan ini

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Update foto profil pengguna.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'], // Wajib, harus gambar, maks 2MB
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Simpan foto baru dan dapatkan path-nya
        $path = $request->file('photo')->store('profile-photos', 'public');

        // Update path foto di database
        $user->update(['profile_photo_path' => $path]);

        return back()->with('status', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Metode untuk update detail profil (email & no. telepon).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi data yang masuk dari form
        $validatedData = $request->validate([
            // Email harus unik, kecuali untuk email pengguna itu sendiri
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // No telepon boleh kosong (nullable), tapi jika diisi harus string maks 20 karakter
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        // 2. Update data pengguna dengan data yang sudah tervalidasi
        $user->update($validatedData);

        // 3. Kembali ke halaman profil dengan pesan sukses
        return back()->with('status', 'Detail profil berhasil diperbarui!');
    }

    // --- PENAMBAHAN FITUR UBAH PASSWORD ---
    /**
     * Metode untuk mengubah password pengguna.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi data yang masuk
        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        // 2. Update password pengguna dengan yang baru
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // 3. Kembali ke halaman profil dengan pesan sukses
        return back()->with('password_status', 'Password berhasil diperbarui!');
    }

    public function updateFcmToken(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        try {
            $user = Auth::user();
            $user->fcm_token = $request->input('token');
            $user->save();

            return response()->json(['message' => 'Token berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan token.'], 500);
        }
    }
}