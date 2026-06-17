<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Server;

use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\TokenRepository;

class AuthorizationController extends Controller
{
    public function __construct(
        protected ClientRepository $clients,
        protected TokenRepository  $tokens,
    ) {}

    public function show(Request $request)
    {
        $clientId = $request->get('client_id');
        $client   = $this->clients->findActive($clientId);

        if (! $client) {
            abort(400, 'OAuth client not found or inactive.');
        }

        return view('omni::server.authorize', [
            'client'   => $client,
            'user'     => $request->user(),
            'scopes'   => $request->get('scope', '*'),
            'request'  => $request,
        ]);
    }

    public function approve(Request $request)
    {
        return app(\Laravel\Passport\Http\Controllers\ApproveAuthorizationController::class)
            ->approve($request, new PsrResponse);
    }

    public function deny(Request $request)
    {
        return app(\Laravel\Passport\Http\Controllers\DenyAuthorizationController::class)
            ->deny($request, new PsrResponse);
    }
}
