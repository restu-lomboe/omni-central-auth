<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;
use DeveloperAwam\OmniCentralAuth\Models\AuditLog;

class LoginController extends Controller
{
    /**
     * Redirect user ke SSO Server untuk login.
     */
    public function redirect()
    {
        return Socialite::driver('omni')->redirect();
    }

    /**
     * Logout dari aplikasi client dan opsional redirect ke SSO Server logout.
     */
    public function logout(Request $request)
    {
        AuditLog::record('logout');

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $serverUrl = config('omni-central-auth.client.server_url');

        // Redirect ke SSO Server logout agar sesi global juga dihapus
        return redirect("{$serverUrl}/logout");
    }
}
