<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bukti_pembayarans', function (Blueprint $table) {
            $table->string('status')->after('notes')->nullable();
            $table->decimal('jumlah', 15, 2)->after('status')->nullable();
        });
    }

    public function down()
    {
        Schema::table('bukti_pembayarans', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('jumlah');
        });
    }
};