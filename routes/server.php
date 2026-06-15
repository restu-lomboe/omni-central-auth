<?php

use Illuminate\Support\Facades\Route;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Server\AuthorizationController;

// Semua route OAuth2 ditangani oleh Laravel Passport secara otomatis:
// POST /oauth/token
// GET  /oauth/authorize
// POST /oauth/authorize
// DELETE /oauth/authorize
// GET  /oauth/tokens
// dll.

// Route tambahan khusus Omni (opsional, misalnya halaman consent custom)
Route::group([
    'middleware' => config('omni-central-auth.server.middleware', ['web']),
    'prefix'     => 'omni',
], function () {
    // Halaman consent OAuth custom (override default Passport)
    Route::get('/authorize', [AuthorizationController::class, 'show'])->name('omni.authorize');
    Route::post('/authorize', [AuthorizationController::class, 'approve'])->name('omni.authorize.approve');
    Route::delete('/authorize', [AuthorizationController::class, 'deny'])->name('omni.authorize.deny');
});
