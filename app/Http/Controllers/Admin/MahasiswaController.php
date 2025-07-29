<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
 use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    /**
     * Menampilkan daftar mahasiswa dengan fungsionalitas pencarian.
     * Metode ini sekarang bisa merespon permintaan HTML biasa dan permintaan JSON (untuk live search).
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = User::where('is_admin', false);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nim', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $mahasiswa = $query->orderBy('name', 'asc')->paginate(15);

        // Jika request menginginkan JSON (datang dari JavaScript), kirim data JSON.
        if ($request->wantsJson()) {
            return response()->json($mahasiswa);
        }

        // Jika tidak, tampilkan view seperti biasa.
        return view('admin.mahasiswa.index', compact('mahasiswa', 'search'));
    }


 public function store(Request $request)
        {
            $validatedData = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'nim' => ['required', 'string', 'unique:users,nim'], // NIM harus unik
                'email' => ['required', 'email', 'unique:users,email'], // Email harus unik
                'password' => ['required', 'string', 'min:8'], // Password wajib diisi
                'program' => ['nullable', 'string', 'max:255'],
                'tahun_masuk' => ['nullable', 'string', 'max:4'],
                'tanggal_lahir' => ['nullable', 'date'],
                'no_telepon' => ['nullable', 'string', 'max:20'],
            ]);

            // Hash password sebelum disimpan
            $validatedData['password'] = Hash::make($validatedData['password']);

            User::create($validatedData);

            return back()->with('status', 'Mahasiswa baru berhasil ditambahkan!');
        }
    /**
     * Metode untuk update data mahasiswa.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'program' => ['nullable', 'string', 'max:255'],
            'tahun_masuk' => ['nullable', 'string', 'max:4'],
            'tanggal_lahir' => ['nullable', 'date'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validatedData);

        return back()->with('status', 'Data mahasiswa berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Untuk keamanan, pastikan user yang akan dihapus bukan admin
        if ($user->is_admin) {
            return back()->with('error', 'Tidak dapat menghapus akun admin.');
        }
        
        $user->delete();

        return back()->with('status', 'Data mahasiswa berhasil dihapus!');
    }
}
