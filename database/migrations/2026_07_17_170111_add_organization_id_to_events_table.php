<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Nullable dulu supaya event lama (kalau sudah ada data) tidak error.
            // Setelah migration jalan, isi manual/seed organization_id untuk
            // event lama, baru nanti (opsional) diubah jadi NOT NULL.
            $table->foreignId('organization_id')
                  ->nullable()
                  ->after('category_id')
                  ->constrained('organizations')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
        });
    }
};