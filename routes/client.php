<?php

use Illuminate\Support\Facades\Route;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Client\LoginController;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Client\CallbackController;

Route::group([
    'middleware' => ['web'],
    'prefix'     => 'omni',
], function () {
    // Redirect user to the SSO Server for login
    Route::get('/login', [LoginController::class, 'redirect'])->name('omni.login');

    // Callback after the user authorizes on the SSO Server
    Route::get('/callback', [CallbackController::class, 'handle'])->name('omni.callback');

    // Logout (revoke local token, redirect to SSO Server logout)
    Route::post('/logout', [LoginController::class, 'logout'])->name('omni.logout');
});
