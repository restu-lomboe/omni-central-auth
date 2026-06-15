<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Server;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Contracts\AuthorizationViewResponse;

class AuthorizationController extends Controller
{
    public function __construct(
        protected ClientRepository $clients,
        protected TokenRepository  $tokens,
    ) {}

    /**
     * Tampilkan halaman consent OAuth custom.
     */
    public function show(Request $request)
    {
        $clientId = $request->get('client_id');
        $client   = $this->clients->findActive($clientId);

        if (! $client) {
            abort(400, 'OAuth client tidak ditemukan atau tidak aktif.');
        }

        return view('omni::server.authorize', [
            'client'   => $client,
            'user'     => $request->user(),
            'scopes'   => $request->get('scope', '*'),
            'request'  => $request,
        ]);
    }

    /**
     * User menyetujui authorization request.
     */
    public function approve(Request $request)
    {
        // Delegasikan ke Passport's built-in approval
        return app(\Laravel\Passport\Http\Controllers\ApproveAuthorizationController::class)
            ->approve($request);
    }

    /**
     * User menolak authorization request.
     */
    public function deny(Request $request)
    {
        return app(\Laravel\Passport\Http\Controllers\DenyAuthorizationController::class)
            ->deny($request);
    }
}
