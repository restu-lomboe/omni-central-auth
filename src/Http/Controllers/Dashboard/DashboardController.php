<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Dashboard;

use DeveloperAwam\OmniCentralAuth\Models\AuditLog;
use Illuminate\Routing\Controller;
use Laravel\Passport\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_clients' => Client::where('revoked', false)->count(),
            'total_users' => config('omni-central-auth.user_model')::count(),
            'logins_today' => AuditLog::where('event', 'login')
                ->whereDate('occurred_at', today())
                ->count(),
            'failed_today' => AuditLog::where('event', 'login_failed')
                ->whereDate('occurred_at', today())
                ->count(),
        ];

        return view('omni::dashboard.index', compact('stats'));
    }
}
