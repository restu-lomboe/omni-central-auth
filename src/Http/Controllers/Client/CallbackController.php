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
    protected function isPopup(Request $request): bool
    {
        return $request->session()->pull('omni_login_popup', false);
    }

    protected function popupResponse(bool $success)
    {
        return view('omni::auth.popup-callback', ['success' => $success]);
    }

    public function handle(Request $request)
    {
        $ssoData = $request->query('sso_data');

        if (! $ssoData) {
            AuditLog::record('login_failed', ['reason' => 'Missing sso_data parameter']);

            if ($this->isPopup($request)) {
                return $this->popupResponse(false);
            }

            return redirect()->to(config('omni-central-auth.client.server_url') . '/login')
                ->withErrors(['sso' => 'Login SSO gagal. Data tidak ditemukan.']);
        }

        $signingKey = config('omni-central-auth.client.signing_key');

        if (! $signingKey) {
            AuditLog::record('login_failed', ['reason' => 'Signing key not configured']);

            if ($this->isPopup($request)) {
                return $this->popupResponse(false);
            }

            return redirect()->to(config('omni-central-auth.client.server_url') . '/login')
                ->withErrors(['sso' => 'Konfigurasi signing key tidak ditemukan.']);
        }

        $userData = AuthorizationController::decryptPayload($ssoData, $signingKey);

        if (! $userData) {
            AuditLog::record('login_failed', ['reason' => 'Invalid or tampered payload']);

            if ($this->isPopup($request)) {
                return $this->popupResponse(false);
            }

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

        if ($this->isPopup($request)) {
            return $this->popupResponse(true);
        }

        return redirect()->intended(
            config('omni-central-auth.client.home_url', '/dashboard')
        );
    }
}
