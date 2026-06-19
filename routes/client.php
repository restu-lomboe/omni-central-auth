<?php

use Illuminate\Support\Facades\Route;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Client\LoginController;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Client\CallbackController;

Route::group([
    'middleware' => ['web'],
    'prefix'     => 'omni',
], function () {
    // Redirect user ke SSO Server untuk login
    Route::get('/login', [LoginController::class, 'redirect'])->name('omni.login');

    // Callback setelah user authorize di SSO Server
    Route::get('/callback', [CallbackController::class, 'handle'])->name('omni.callback');

    // AJAX callback — popup mengirim sso_data via postMessage, client fetch ke sini
    Route::post('/callback/ajax', [CallbackController::class, 'handleAjax'])->name('omni.callback.ajax');

    // Logout (revoke token lokal, redirect ke SSO Server logout)
    Route::post('/logout', [LoginController::class, 'logout'])->name('omni.logout');
});
