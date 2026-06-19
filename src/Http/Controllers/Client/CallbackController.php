<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\Server\AuthorizationController;
use DeveloperAwam\OmniCentralAuth\Models\AuditLog;

class CallbackController extends Controller
{
    public function handle(Request $request)
    {
        $ssoData = $request->query('sso_data');

        if (! $ssoData) {
            AuditLog::record('login_failed', ['reason' => 'Missing sso_data parameter']);

            return redirect()->to(config('omni-central-auth.client.server_url') . '/login')
                ->withErrors(['sso' => 'Login SSO gagal. Data tidak ditemukan.']);
        }

        $result = $this->processSsoData($ssoData);

        if (! $result['success']) {
            return redirect()->to(config('omni-central-auth.client.server_url') . '/login')
                ->withErrors(['sso' => $result['message']]);
        }

        return redirect()->intended(
            config('omni-central-auth.client.home_url', '/dashboard')
        );
    }

    public function handleAjax(Request $request)
    {
        $ssoData = $request->input('sso_data');

        if (! $ssoData) {
            return response()->json([
                'success' => false,
                'message' => 'Missing sso_data',
            ], 400);
        }

        $result = $this->processSsoData($ssoData);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    protected function processSsoData(string $ssoData): array
    {
        $signingKey = config('omni-central-auth.client.signing_key');

        if (! $signingKey) {
            AuditLog::record('login_failed', ['reason' => 'Signing key not configured']);

            return ['success' => false, 'message' => 'Signing key not configured'];
        }

        $userData = AuthorizationController::decryptPayload($ssoData, $signingKey);

        if (! $userData) {
            AuditLog::record('login_failed', ['reason' => 'Invalid or tampered payload']);

            return ['success' => false, 'message' => 'Invalid or tampered payload'];
        }

        $userModel = config('omni-central-auth.user_model');

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
            'via'     => 'sso',
            'omni_id' => $userData['omni_id'],
        ]);

        return ['success' => true, 'message' => 'Login successful'];
    }
}
