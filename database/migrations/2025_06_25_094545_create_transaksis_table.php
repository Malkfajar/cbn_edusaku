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
    Schema::create('transaksis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tagihan_id')->constrained('tagihans')->onDelete('cascade');
        $table->string('order_id')->unique();
        $table->decimal('jumlah_bayar', 15, 2);
        $table->enum('status_transaksi', ['pending', 'success', 'failed', 'expired'])->default('pending');
        $table->string('snap_token')->nullable();
        $table->string('payment_method')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
