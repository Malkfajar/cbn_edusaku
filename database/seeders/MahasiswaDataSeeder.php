<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MahasiswaDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = database_path('data/data_login.csv');

        if (!file_exists($csvFile) || !is_readable($csvFile)) {
            $this->command->error("File CSV tidak ditemukan atau tidak bisa dibaca di: " . $csvFile);
            return;
        }

        $header = null;
        $rowNumber = 0;
        $headerRowNumber = 4; // Header tetap di baris ke-4

        if (($handle = fopen($csvFile, 'r')) !== false) {
            $this->command->info('Membaca file CSV dengan struktur kolom baru...');

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;

                if ($rowNumber === $headerRowNumber) {
                    // Membersihkan header dari spasi dan mengubah ke huruf kecil
                    $header = array_map('trim', $row);
                    $header = array_map('strtolower', $header);
                    continue;
                }

                if ($rowNumber > $headerRowNumber && $header !== null) {
                    
                    if (count($header) !== count($row)) {
                        Log::warning("Melewatkan baris {$rowNumber} karena jumlah kolom tidak sesuai header.");
                        continue;
                    }
                    
                    $data = array_combine($header, $row);

                    // Membersihkan setiap data dari spasi tersembunyi
                    $nim = trim($data['nim']);
                    $nama = trim($data['nama']);
                    $password = trim($data['password']);

                    if (empty($nim) || empty($nama) || empty($password)) {
                         Log::warning("Melewatkan baris {$rowNumber} karena data NIM, nama, atau password kosong.");
                         continue;
                    }

                    // Menyiapkan data untuk dimasukkan ke database, termasuk kolom baru
                    $userData = [
                        'name'          => $nama,
                        'email'         => trim($data['email']) ?: $nim . '@kampus.ac.id', // Gunakan email dari CSV, jika kosong buat default
                        'program'       => trim($data['program']) ?: null,
                        'tahun_masuk'   => trim($data['tahun masuk']) ?: null,
                        'tanggal_lahir' => trim($data['tanggal lahir']) ? date('Y-m-d', strtotime(trim($data['tanggal lahir']))) : null,
                        'no_telepon'    => trim($data['no. telepon']) ?: null,
                        'password'      => Hash::make($password),
                        'username'      => null,
                        'is_admin'      => false,
                    ];

                    try {
                        // Cari berdasarkan NIM, lalu update atau buat baru
                        User::updateOrCreate(['nim' => $nim], $userData);
                    } catch (\Exception $e) {
                        $this->command->error("Gagal memproses NIM {$nim} pada baris {$rowNumber}: " . $e->getMessage());
                    }
                }
            }
            fclose($handle);
            $this->command->info('Seeding data mahasiswa dari CSV berhasil.');
        }
    }
}
