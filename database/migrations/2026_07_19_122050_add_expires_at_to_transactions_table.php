<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Waktu batas transaksi ini harus lunas, sebelum reservasi
            // stok dilepas otomatis kembali ke event.stock.
            $table->timestamp('expires_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};