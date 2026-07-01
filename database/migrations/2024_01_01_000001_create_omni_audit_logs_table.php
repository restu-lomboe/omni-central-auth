<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('omni_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event');           // login, logout, login_failed, token_issued, dll.
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('client_app')->nullable(); // nama OAuth client yang dipakai
            $table->json('metadata')->nullable();     // data tambahan (country, device, dll.)
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['user_id', 'event']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omni_audit_logs');
    }
};
