<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Diisi timestamp saat panitia scan QR code peserta di lokasi event.
            // NULL = peserta belum hadir / belum check-in.
            $table->timestamp('checked_in_at')->nullable()->after('status');

            // Path file PDF sertifikat yang sudah digenerate (disk 'local' atau 'public').
            // Supaya tidak generate ulang PDF setiap kali dibutuhkan.
            $table->string('certificate_path')->nullable()->after('checked_in_at');

            // Menandai kapan email sertifikat berhasil terkirim.
            // Dipakai untuk mencegah pengiriman ganda (double-send) via Job/Queue.
            $table->timestamp('certificate_sent_at')->nullable()->after('certificate_path');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['checked_in_at', 'certificate_path', 'certificate_sent_at']);
        });
    }
};