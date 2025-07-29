<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; // Pastikan ini ada

class ProfileController extends Controller
{
    /**
     * Menampilkan form untuk edit profil admin.
     * File view 'admin.profile.edit' akan kita buat di Langkah 2.
     */
    public function edit()
    {
        return view('admin.profile.profile', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Memperbarui informasi dasar (nama & email) admin.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        // Kembali ke halaman edit dengan pesan sukses.
        return redirect()->route('admin.profile.edit')->with('status', 'Profil berhasil diperbarui!');
    }

    /**
     * Memperbarui password admin.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Kembali ke halaman edit dengan pesan sukses.
        return redirect()->route('admin.profile.edit')->with('status', 'Password berhasil diubah!');
    }

  public function updatePhoto(Request $request)
{
    // 1. Validasi file yang diunggah
    $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $user = Auth::user();
    $folderPath = public_path('profile-photos');

    // 2. Hapus foto lama jika ada
    if ($user->profile_photo_path && File::exists($folderPath . '/' . $user->profile_photo_path)) {
        File::delete($folderPath . '/' . $user->profile_photo_path);
    }

    // 3. Siapkan file baru dan buat nama unik
    $file = $request->file('photo');
    $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

    // 4. Pindahkan file baru langsung ke public/profile-photos
    $file->move($folderPath, $fileName);

    // 5. Simpan nama file baru ke database
    $user->profile_photo_path = $fileName;
    $user->save();

    return back()->with('status', 'Foto profil berhasil diperbarui!');
}
}