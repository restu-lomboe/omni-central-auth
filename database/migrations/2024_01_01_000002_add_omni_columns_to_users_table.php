<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini digunakan di aplikasi CLIENT (mode: client / both).
 * Menambahkan kolom SSO ke tabel users yang sudah ada agar bisa
 * menyimpan data dari SSO Server tanpa menyimpan password lokal.
 *
 * CATATAN: Jika tabel users belum ada, jalankan dulu:
 *   php artisan migrate (untuk membuat tabel users default Laravel)
 * Kemudian jalankan migration ini.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ID user di SSO Server (Identity Provider)
            $table->string('omni_id')->nullable()->after('id');

            // Access token OAuth2 yang aktif
            $table->text('omni_token')->nullable()->after('omni_id');

            // Refresh token untuk memperbarui access token
            $table->text('omni_refresh_token')->nullable()->after('omni_token');

            // Password dibuat nullable karena client app tidak menyimpan password
            // Jalankan ini hanya jika kolom password belum nullable
            // $table->string('password')->nullable()->change();

            $table->index('omni_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['omni_id']);
            $table->dropColumn(['omni_id', 'omni_token', 'omni_refresh_token']);
        });
    }
};
