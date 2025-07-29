<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // Jalankan operasi yang tidak bermasalah terlebih dahulu (jika belum berjalan)
    Schema::table('tagihans', function (Blueprint $table) {
        if (Schema::hasColumn('tagihans', 'jumlah_tagihan') && !Schema::hasColumn('tagihans', 'jumlah_total')) {
            $table->renameColumn('jumlah_tagihan', 'jumlah_total');
        }
    });

    Schema::table('tagihans', function (Blueprint $table) {
        if (!Schema::hasColumn('tagihans', 'sisa_tagihan')) {
            $table->decimal('sisa_tagihan', 15, 2)->after('jumlah_total');
        }
        if (!Schema::hasColumn('tagihans', 'izinkan_cicilan')) {
            $table->boolean('izinkan_cicilan')->default(false)->after('status');
        }
    });

    // LANGKAH A: PERBAIKI DATA LAMA
    // Ubah semua entri 'Lunas' (huruf besar) menjadi 'lunas' (huruf kecil)
    DB::table('tagihans')->where('status', 'Lunas')->update(['status' => 'lunas']);

    // LANGKAH B: PERBAIKI STRUKTUR DENGAN ENUM YANG BENAR (TANPA DUPLIKAT)
    // Setelah data bersih, kita bisa mengubah struktur kolom dengan aman.
    DB::statement("ALTER TABLE tagihans MODIFY COLUMN status ENUM('belum_dibayar', 'belum_lunas', 'lunas') NOT NULL DEFAULT 'belum_dibayar'");
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->renameColumn('jumlah_total', 'jumlah_tagihan');
            $table->dropColumn('sisa_tagihan');
            $table->string('status')->change(); // Kembalikan ke tipe data string biasa jika di-rollback
            $table->dropColumn('izinkan_cicilan');
        });
    }
};
