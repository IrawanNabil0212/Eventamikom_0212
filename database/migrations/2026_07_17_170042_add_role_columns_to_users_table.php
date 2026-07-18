<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Catatan penting:
     * - Kolom `role` SUDAH ada di tabel users (default 'user'), jadi kita
     *   tidak buat ulang, cukup NORMALISASI datanya + tambah kolom baru.
     * - `password` diubah jadi nullable karena user yang daftar via
     *   Google (Socialite) tidak akan punya password.
     */
    public function up(): void
    {
        // 1. Normalisasi value role lama: 'user' -> 'buyer', 'admin' tetap 'admin'
        //    (kita pertahankan 'admin' apa adanya supaya AdminMiddleware yang
        //    sudah ada tetap jalan tanpa perlu diubah)
        DB::table('users')->where('role', 'user')->update(['role' => 'buyer']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('avatar')->nullable()->after('google_id');

            $table->foreignId('organization_id')
                  ->nullable()
                  ->after('role')
                  ->constrained('organizations')
                  ->nullOnDelete();

            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
            $table->dropColumn(['google_id', 'avatar']);
            $table->string('password')->nullable(false)->change();
        });

        DB::table('users')->where('role', 'buyer')->update(['role' => 'user']);
    }
};