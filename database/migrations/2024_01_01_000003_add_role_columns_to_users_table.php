<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini digunakan di aplikasi SERVER (mode: server / both).
 * Menambahkan kolom role management ke tabel users untuk keperluan
 * Admin Dashboard Omni Central Auth.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Role sederhana: 'user' | 'admin'
            $table->string('role')->default('user')->after('remember_token');

            // Flag boolean untuk admin check yang lebih cepat
            $table->boolean('is_admin')->default(false)->after('role');

            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'is_admin']);
        });
    }
};
