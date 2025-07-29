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
        // Tambahkan kolom untuk biaya admin setelah jumlah_bayar
        $table->decimal('biaya_admin', 15, 2)->default(0)->after('jumlah_bayar');
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
