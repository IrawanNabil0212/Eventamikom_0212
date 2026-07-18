<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Nullable: transaksi lama (sebelum SSO ada) tidak punya user_id.
            // Transaksi baru WAJIB diisi dari controller checkout setelah
            // buyer login via Google.
            $table->foreignId('user_id')
                  ->nullable()
                  ->after('event_id')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};