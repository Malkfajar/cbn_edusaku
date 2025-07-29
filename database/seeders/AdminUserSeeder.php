<?php

    namespace Database\Seeders;

    use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Illuminate\Database\Seeder;
    use App\Models\User;
    use Illuminate\Support\Facades\Hash;

    class AdminUserSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            User::create([
                'name' => 'Admin Keuangan',
                'username' => 'admin', // Username untuk login
                'email' => 'admin@keuangan.app', // Email dummy
                'nim' => 'ADMIN001', // NIM dummy, bisa diisi apa saja
                'password' => Hash::make('password123'), // Password yang Anda minta
                'is_admin' => true, // Menandakan ini adalah akun admin
            ]);
        }
    }
    