<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OmniAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Cek apakah user punya role admin
        // Developer bisa override method ini via config user_model
        $user = $request->user();

        $hasAdminAccess = match (true) {
            // Jika model punya method isOmniAdmin() — custom logic
            method_exists($user, 'isOmniAdmin') => $user->isOmniAdmin(),
            // Jika model punya kolom/attribute is_admin
            isset($user->is_admin)              => (bool) $user->is_admin,
            // Jika model punya kolom role
            isset($user->role)                  => in_array($user->role, ['admin', 'super_admin']),
            // Default: tolak akses
            default                             => false,
        };

        if (! $hasAdminAccess) {
            abort(403, 'Access denied. You are not an Omni Central Auth admin.');
        }

        return $next($request);
    }
}
