<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('transaksis', function (Blueprint $table) {
        // Kita buat kolom tagihan_id bisa null, karena akan digantikan fungsinya
        $table->unsignedBigInteger('tagihan_id')->nullable()->change();
        // Kolom baru untuk menyimpan semua ID tagihan dalam format JSON
        $table->json('paid_tagihan_ids')->after('tagihan_id')->nullable();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            //
        });
    }
};
