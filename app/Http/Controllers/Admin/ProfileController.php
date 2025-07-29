<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

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
    $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $user = Auth::user();

    if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
        Storage::disk('public')->delete($user->profile_photo_path);
    }

    $path = $request->file('photo')->store('profile-photos', 'public');
    
    // UBAH BAGIAN INI: dari 'photo' menjadi 'profile_photo_path'
    $user->update(['profile_photo_path' => $path]);

    return redirect()->route('admin.profile.edit')->with('status', 'Foto profil berhasil diperbarui!');
}
}