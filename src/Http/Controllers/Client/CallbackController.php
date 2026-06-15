<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Client;

use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;
use DeveloperAwam\OmniCentralAuth\Models\AuditLog;

class CallbackController extends Controller
{
    /**
     * Handle callback dari SSO Server setelah user authorize.
     */
    public function handle()
    {
        try {
            $ssoUser = Socialite::driver('omni')->user();
        } catch (\Exception $e) {

            AuditLog::record('login_failed', ['reason' => $e->getMessage()]);

            return redirect()->to(config('omni-central-auth.client.server_url') . '/login')
                ->withErrors(['sso' => 'Login SSO gagal. Silakan coba lagi.']);
        }

        $userModel = config('omni-central-auth.user_model');

        // Cari atau buat user lokal berdasarkan email dari SSO Server
        $localUser = $userModel::firstOrCreate(
            ['email' => $ssoUser->getEmail()],
            [
                'name'              => $ssoUser->getName(),
                'email'             => $ssoUser->getEmail(),
                'password'          => null, // tidak pakai password lokal
                'omni_id'           => $ssoUser->getId(),
                'omni_token'        => $ssoUser->token,
                'omni_refresh_token'=> $ssoUser->refreshToken,
            ]
        );

        // Update token jika user sudah ada
        if (! $localUser->wasRecentlyCreated) {
            $localUser->update([
                'omni_token'        => $ssoUser->token,
                'omni_refresh_token'=> $ssoUser->refreshToken,
            ]);
        }

        auth()->login($localUser, true);

        AuditLog::record('login', [
            'via'      => 'sso',
            'omni_id'  => $ssoUser->getId(),
        ]);

        return redirect()->intended(
            config('omni-central-auth.client.home_url', '/dashboard')
        );
    }
}
