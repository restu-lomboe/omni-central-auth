<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Server\AuthorizationController;
use DeveloperAwam\OmniCentralAuth\Models\AuditLog;

class CallbackController extends Controller
{
    /**
     * Handle callback dari SSO Server — data user langsung dari encrypted payload.
     */
    public function handle(Request $request)
    {
        $ssoData = $request->query('sso_data');

        if (! $ssoData) {
            AuditLog::record('login_failed', ['reason' => 'Missing sso_data parameter']);

            return redirect()->to(config('omni-central-auth.client.server_url') . '/login')
                ->withErrors(['sso' => 'Login SSO gagal. Data tidak ditemukan.']);
        }

        $signingKey = config('omni-central-auth.client.signing_key');

        if (! $signingKey) {
            AuditLog::record('login_failed', ['reason' => 'Signing key not configured']);

            return redirect()->to(config('omni-central-auth.client.server_url') . '/login')
                ->withErrors(['sso' => 'Konfigurasi signing key tidak ditemukan.']);
        }

        $userData = AuthorizationController::decryptPayload($ssoData, $signingKey);

        if (! $userData) {
            AuditLog::record('login_failed', ['reason' => 'Invalid or tampered payload']);

            return redirect()->to(config('omni-central-auth.client.server_url') . '/login')
                ->withErrors(['sso' => 'Data login tidak valid. Silakan coba lagi.']);
        }

        $userModel = config('omni-central-auth.user_model');

        // Cari atau buat user lokal berdasarkan email dari SSO Server
        $localUser = $userModel::firstOrCreate(
            ['email' => $userData['email']],
            [
                'name'     => $userData['name'],
                'email'    => $userData['email'],
                'password' => null,
                'omni_id'  => $userData['omni_id'],
            ]
        );

        auth()->login($localUser, true);

        AuditLog::record('login', [
            'via'      => 'sso',
            'omni_id'  => $userData['omni_id'],
        ]);

        return redirect()->intended(
            config('omni-central-auth.client.home_url', '/dashboard')
        );
    }
}
