<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Log;

class TagihanController extends Controller
{
    /**
     * Menampilkan daftar semua tagihan.
     */
public function index(Request $request)
        {
            $search = $request->query('search');
            $query = Tagihan::with('user')->whereHas('user', function ($q) {
                $q->where('is_admin', false);
            });

             if ($search) {
            // Ubah input pencarian menjadi format yang cocok untuk kolom status
            // Contoh: "Belum Lunas" -> "belum_lunas"
            $statusSearchTerm = str_replace(' ', '_', strtolower($search));

            $query->where(function ($q) use ($search, $statusSearchTerm) {
                // 1. Cari berdasarkan Nama Tagihan
                $q->where('nama_tagihan', 'like', '%' . $search . '%')
                
                // 2. ATAU Cari berdasarkan Status yang sudah diubah formatnya
                  ->orWhere('status', 'like', '%' . $statusSearchTerm . '%') 
                
                // 3. ATAU Cari berdasarkan relasi ke User (Nama atau NIM)
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('nim', 'like', '%' . $search . '%');
                  });
            });
        }
            
            $tagihan = $query->latest()->paginate(15);
            $mahasiswa = User::where('is_admin', false)->orderBy('name')->get();

            // Ambil data unik untuk filter di modal
            $programs = User::where('is_admin', false)->whereNotNull('program')->distinct()->pluck('program');
            $angkatans = User::where('is_admin', false)->whereNotNull('tahun_masuk')->distinct()->pluck('tahun_masuk');

            return view('admin.tagihan.index', compact('tagihan', 'mahasiswa', 'search', 'programs', 'angkatans'));
        }
    /**
     * Menyimpan tagihan baru.
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'user_id' => 'required|exists:users,id',
        'semester' => 'required|integer|min:1',
        'nama_tagihan' => 'required|string|max:255',
        'jumlah_total' => 'required|numeric|min:0', // Ganti nama field
    ]);

    // Tambahkan logika untuk sisa_tagihan dan status default
    $validatedData['sisa_tagihan'] = $validatedData['jumlah_total'];
    $validatedData['status'] = 'belum_dibayar';

    Tagihan::create($validatedData);

    return back()->with('status', 'Tagihan baru berhasil ditambahkan!');
}

     public function storeBulk(Request $request)
        {
            $validatedData = $request->validate([
                'nama_tagihan' => 'required|string|max:255',
                'jumlah_total' => 'required|numeric|min:0',
                'semester' => 'required|integer|min:1',
                'program' => 'required|string',
                'tahun_masuk' => 'required|string',
            ]);

            // Query untuk mencari mahasiswa target
            $query = User::where('is_admin', false);

            if ($validatedData['program'] !== 'semua') {
                $query->where('program', $validatedData['program']);
            }

            if ($validatedData['tahun_masuk'] !== 'semua') {
                $query->where('tahun_masuk', $validatedData['tahun_masuk']);
            }

            $targetMahasiswa = $query->get();
            $count = 0;

    foreach ($targetMahasiswa as $mhs) {
        Tagihan::create([
            'user_id' => $mhs->id,
            'semester' => $validatedData['semester'],
            'nama_tagihan' => $validatedData['nama_tagihan'],
            'jumlah_total' => $validatedData['jumlah_total'],
            'sisa_tagihan' => $validatedData['jumlah_total'], // Tambahkan ini
            'status' => 'belum_dibayar', // Gunakan status baru
        ]);
        $count++;

    }

            return back()->with('status', "Berhasil membuat {$count} tagihan baru secara massal.");
        }
    /**
     * Mengupdate data tagihan yang sudah ada.
     */
   public function update(Request $request, Tagihan $tagihan)
{
    // DIUBAH: Validasi disesuaikan dengan nama input 'jumlah_total'
    $validatedData = $request->validate([
        'user_id' => 'required|exists:users,id',
        'semester' => 'required|integer|min:1',
        'nama_tagihan' => 'required|string|max:255',
        'jumlah_total' => 'required|numeric|min:0', // <- Perubahan di sini
    ]);

    // DITAMBAHKAN: Logika cerdas untuk menyesuaikan 'sisa_tagihan' secara otomatis
    // saat 'jumlah_total' diubah.
    if (isset($validatedData['jumlah_total'])) {
        // Hitung selisih antara total lama dan total baru
        $selisih = $tagihan->jumlah_total - $validatedData['jumlah_total'];
        
        // Sesuaikan sisa tagihan berdasarkan selisih tersebut
        $sisaTagihanBaru = $tagihan->sisa_tagihan - $selisih;

        // Pastikan sisa tagihan tidak menjadi negatif
        $validatedData['sisa_tagihan'] = max(0, $sisaTagihanBaru);
    }
    // --- Akhir dari logika tambahan ---

    $tagihan->update($validatedData);

    return back()->with('status', 'Tagihan berhasil diperbarui!');
}

     public function import(Request $request)
        {
            // 1. Validasi file yang di-upload
            $request->validate([
                'file' => 'required|mimes:csv,txt|max:2048',
            ]);

            $file = $request->file('file');
            $path = $file->getRealPath();
            
            // 2. Buka dan baca file CSV
            $handle = fopen($path, "r");
            $header = true;
            $successCount = 0;
            $failCount = 0;

            // Loop melalui setiap baris di CSV
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Lewati baris header
                if ($header) {
                    $header = false;
                    continue;
                }

                try {
                    // Asumsi urutan kolom: nim, semester, nama_tagihan, jumlah_tagihan, status
                    $nim = trim($row[0]);
                    
                    // Cari user berdasarkan NIM
                    $user = User::where('nim', $nim)->first();

                    if ($user) {
                        // Jika user ditemukan, buat tagihan
                        Tagihan::create([
                            'user_id'        => $user->id,
                            'semester'       => trim($row[1]),
                            'nama_tagihan'   => trim($row[2]),
                            'jumlah_tagihan' => trim($row[3]),
                            'status'         => trim($row[4]) ?? 'Belum Dibayar',
                        ]);
                        $successCount++;
                    } else {
                        // Jika user tidak ditemukan, catat sebagai gagal
                        $failCount++;
                        Log::warning("Import tagihan gagal: NIM tidak ditemukan - " . $nim);
                    }
                } catch (\Exception $e) {
                    $failCount++;
                    Log::error("Error saat impor baris CSV: " . $e->getMessage());
                    continue;
                }
            }

            fclose($handle);

            $message = "Berhasil mengimpor {$successCount} tagihan.";
            if ($failCount > 0) {
                $message .= " Gagal mengimpor {$failCount} tagihan (NIM tidak ditemukan atau data tidak valid).";
            }

            return back()->with('status', $message);
        }

        public function toggleInstallment(Tagihan $tagihan)
{
    // Ubah nilai boolean (jika true jadi false, jika false jadi true)
    $tagihan->izinkan_cicilan = !$tagihan->izinkan_cicilan;
    $tagihan->save();

    $message = $tagihan->izinkan_cicilan ? 'Pembayaran cicilan diaktifkan.' : 'Pembayaran cicilan dinonaktifkan.';

    return back()->with('status', $message);
}

}
