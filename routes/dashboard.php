<?php

use Illuminate\Support\Facades\Route;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Dashboard\DashboardController;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Dashboard\ClientController;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Dashboard\UserController;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Dashboard\AuditLogController;

$prefix     = config('omni-central-auth.dashboard.prefix', 'omni-dashboard');
$middleware = config('omni-central-auth.dashboard.middleware', ['web', 'auth', 'omni.admin']);

Route::group([
    'prefix'     => $prefix,
    'middleware' => $middleware,
    'as'         => 'omni.dashboard.',
], function () {

    // Overview
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // OAuth Clients
    Route::resource('clients', ClientController::class)->except(['show']);

    // Users & Roles
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Audit Log
    Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

});
