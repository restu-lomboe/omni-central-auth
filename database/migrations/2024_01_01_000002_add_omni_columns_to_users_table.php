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
            // ID user di SSO Server (Identity Provider) — referensi user lintas sistem
            $table->string('omni_id')->nullable()->after('id');

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
            $table->dropColumn('omni_id');
        });
    }
};
