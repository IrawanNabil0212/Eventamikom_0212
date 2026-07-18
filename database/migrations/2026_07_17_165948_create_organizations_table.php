<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();

            // Pemilik/pendaftar organisasi ini. Nullable dulu supaya
            // urutan pembuatan (user dulu / organization dulu) fleksibel,
            // tapi di alur registrasi nanti selalu diisi.
            $table->foreignId('owner_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('name');                 // "HIMA Teknik Informatika"
            $table->string('slug')->unique();        // untuk URL halaman profil publik
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('phone')->nullable();

            // pending -> menunggu approval Superadmin
            // approved -> boleh bikin event & masuk dashboard
            // rejected -> ditolak, tidak boleh akses dashboard organizer
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};