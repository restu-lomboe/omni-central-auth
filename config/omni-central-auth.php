<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mode
    |--------------------------------------------------------------------------
    | Tentukan peran aplikasi ini dalam ekosistem SSO.
    |
    | 'server' → Aplikasi ini adalah Identity Provider (IdP / SSO Server)
    | 'client' → Aplikasi ini adalah Service Provider (SP / Aplikasi Klien)
    | 'both'   → Keduanya aktif (untuk development / monorepo)
    |
    */
    'mode' => env('OMNI_AUTH_MODE', 'server'),

    /*
    |--------------------------------------------------------------------------
    | Server Settings (aktif jika mode = 'server' atau 'both')
    |--------------------------------------------------------------------------
    */
    'server' => [
        // Nama aplikasi SSO ini (tampil di halaman consent OAuth)
        'app_name' => env('OMNI_SERVER_APP_NAME', config('app.name')),

        // Aktifkan fitur pendaftaran user baru
        'registration' => env('OMNI_SERVER_REGISTRATION', true),

        // Aktifkan Two-Factor Authentication
        'two_factor_auth' => env('OMNI_SERVER_2FA', true),

        // Aktifkan Passkeys (WebAuthn) — memerlukan package tambahan
        'passkeys' => env('OMNI_SERVER_PASSKEYS', false),

        // URL redirect default setelah login berhasil
        'home_url' => env('OMNI_SERVER_HOME', '/user'),

        // Signing key untuk encrypt data user yang dikirim ke client callback
        // HARUS sama dengan yang diset di client
        'signing_key' => env('OMNI_CENTRAL_SIGNING_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Client Settings (aktif jika mode = 'client' atau 'both')
    |--------------------------------------------------------------------------
    */
    'client' => [
        // URL lengkap SSO Server (IdP)
        'server_url' => env('OMNI_CLIENT_SERVER_URL', 'http://localhost:8000'),

        // OAuth2 Client Credentials (didapat dari Admin Dashboard IdP)
        'client_id'     => env('OMNI_CLIENT_ID'),
        'client_secret' => env('OMNI_CLIENT_SECRET'),

        // Redirect URI setelah user authorize di SSO Server
        'redirect_uri' => env('OMNI_CLIENT_REDIRECT_URI', '/omni/callback'),

        // Scopes yang diminta
        'scopes' => ['*'],

        // URL redirect setelah login berhasil di sisi client
        'home_url' => env('OMNI_CLIENT_HOME', '/dashboard'),

        // Label tombol login (tampil di halaman login client)
        'button_label' => env('OMNI_CLIENT_BUTTON_LABEL', 'Login with Central Account'),

        // Signing key untuk verifikasi payload dari SSO Server
        // HARUS sama dengan yang diset di server
        'signing_key' => env('OMNI_CENTRAL_SIGNING_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard
    |--------------------------------------------------------------------------
    */
    'dashboard' => [
        'enabled' => env('OMNI_DASHBOARD_ENABLED', true),

        // Route prefix untuk dashboard
        'prefix' => env('OMNI_DASHBOARD_PREFIX', 'omni-dashboard'),

        // Middleware yang melindungi dashboard
        'middleware' => ['web', 'auth', 'omni.admin'],

        // Fitur yang ditampilkan
        'features' => [
            'clients'   => true,  // Manage OAuth Clients
            'users'     => true,  // Manage Users & Roles
            'audit_log' => true,  // Audit Log
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    */
    'load_migrations' => true,

    // Model User yang dipakai (bisa di-override)
    'user_model' => env('OMNI_USER_MODEL', \App\Models\User::class),

    /*
    |--------------------------------------------------------------------------
    | Audit Log
    |--------------------------------------------------------------------------
    */
    'audit' => [
        // Berapa lama (hari) audit log disimpan, null = selamanya
        'retention_days' => env('OMNI_AUDIT_RETENTION', 90),

        // Event yang dicatat
        'events' => [
            'login'          => true,
            'logout'         => true,
            'login_failed'   => true,
            'token_issued'   => true,
            'token_revoked'  => true,
            '2fa_challenged' => true,
        ],
    ],

];
