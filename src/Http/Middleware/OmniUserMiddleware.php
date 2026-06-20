<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OmniUserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Allow only if explicit role 'user'
        $isRegularUser = match (true) {
            method_exists($user, 'isOmniUser') => $user->isOmniUser(),
            isset($user->role)                 => $user->role === 'user',
            default                            => ! (isset($user->is_admin) && $user->is_admin),
        };

        if (! $isRegularUser) {
            abort(403, 'Access denied. This page is for regular users only.');
        }

        return $next($request);
    }
}