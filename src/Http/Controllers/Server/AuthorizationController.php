<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers\Server;

use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Passport\Client;
use Laravel\Passport\TokenRepository;

class AuthorizationController extends Controller
{
    public function __construct(
        protected TokenRepository  $tokens,
    ) {}

    public function show(Request $request)
    {
        $clientId = $request->get('client_id');
        $client   = Client::find($clientId);

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
        $user = $request->user();
        $clientId = $request->get('client_id');
        $client = Client::find($clientId);

        if (! $client) {
            abort(400, 'OAuth client not found or inactive.');
        }

        $signingKey = config('omni-central-auth.server.signing_key');

        if (! $signingKey) {
            abort(500, 'SSO signing key not configured. Set OMNI_CENTRAL_SIGNING_KEY in .env');
        }

        $payload = $this->encryptPayload([
            'omni_id'   => $user->getAuthIdentifier(),
            'name'      => $user->name,
            'email'     => $user->email,
            'avatar'    => $user->avatar ?? null,
            'timestamp' => now()->timestamp,
        ], $signingKey);

        $raw = $client->getAttributes()['redirect_uris'] ?? null;
        $redirects = [];

        if ($raw !== null) {
            $decoded = json_decode($raw, true);

            if (is_array($decoded)) {
                $redirects = $decoded;
            } elseif ($decoded !== null && $decoded !== false) {
                // decoded JSON is a scalar (string/number) — wrap it
                $redirects = [$decoded];
            } else {
                // Fallback: if raw is a plain string (not JSON), use it
                $redirects = is_string($raw) && $raw !== '' ? [$raw] : [];
            }
        }

        $redirectUri = $redirects[0] ?? '';
        $separator = parse_url($redirectUri, PHP_URL_QUERY) ? '&' : '?';

        return redirect("{$redirectUri}{$separator}sso_data=" . urlencode($payload));
    }

    public function deny(Request $request)
    {
        return app(\Laravel\Passport\Http\Controllers\DenyAuthorizationController::class)
            ->deny($request, new PsrResponse);
    }

    public static function encryptPayload(array $data, string $key): string
    {
        $json = json_encode($data);
        $encryptionKey = substr(hash('sha256', $key, true), 0, 32);
        $iv = random_bytes(16);

        $encrypted = openssl_encrypt(
            $json, 'aes-256-cbc', $encryptionKey, OPENSSL_RAW_DATA, $iv
        );

        return base64_encode($iv . $encrypted);
    }

    public static function decryptPayload(string $payload, string $key): ?array
    {
        $decoded = base64_decode($payload, true);

        if ($decoded === false || strlen($decoded) < 16) {
            return null;
        }

        $encryptionKey = substr(hash('sha256', $key, true), 0, 32);
        $iv = substr($decoded, 0, 16);
        $encrypted = substr($decoded, 16);

        $decrypted = openssl_decrypt(
            $encrypted, 'aes-256-cbc', $encryptionKey, OPENSSL_RAW_DATA, $iv
        );

        if ($decrypted === false) {
            return null;
        }

        $data = json_decode($decrypted, true);

        if (! is_array($data) || ! isset($data['omni_id'], $data['name'], $data['email'])) {
            return null;
        }

        return $data;
    }
}
