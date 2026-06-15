<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DeveloperAwam\OmniCentralAuth\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->event, fn ($q) => $q->where('event', $request->event))
            ->when($request->search, fn ($q) => $q->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->orderByDesc('occurred_at')
            ->paginate(50);

        $events = AuditLog::distinct()->pluck('event');

        return view('omni::dashboard.audit-log.index', compact('logs', 'events'));
    }
}
