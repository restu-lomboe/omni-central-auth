<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Server\AuthorizationController;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Server\UserProfileController;

// All OAuth2 routes are handled by Laravel Passport automatically:
// POST /oauth/token
// GET  /oauth/authorize
// POST /oauth/authorize
// DELETE /oauth/authorize
// GET  /oauth/tokens
// etc.

// API endpoint for client apps to fetch user data after SSO login
Route::middleware('auth:api')->get('/api/user', function (Request $request) {
    $user = $request->user();

    return [
        'id'     => $user->getAuthIdentifier(),
        'name'   => $user->name,
        'email'  => $user->email,
        'avatar' => $user->avatar ?? null,
    ];
});

// User dashboard — 'user' role only
Route::middleware(['web', 'auth', 'omni.user'])->group(function () {
    Route::get('/user', function () {
        return view('omni::dashboard.user-page');
    })->name('user.dashboard');

    Route::put('/user', [UserProfileController::class, 'update'])->name('user.profile.update');
    Route::post('/user/password', [UserProfileController::class, 'updatePassword'])->name('user.password.update');
});

// Additional Omni routes (optional, e.g. custom consent page)
Route::group([
    'middleware' => config('omni-central-auth.server.middleware', ['web']),
    'prefix'     => 'omni',
], function () {
    // Custom OAuth consent page (overrides Passport default)
    Route::get('/authorize', [AuthorizationController::class, 'show'])->name('omni.authorize');
    Route::post('/authorize', [AuthorizationController::class, 'approve'])->name('omni.authorize.approve');
    Route::delete('/authorize', [AuthorizationController::class, 'deny'])->name('omni.authorize.deny');
});
