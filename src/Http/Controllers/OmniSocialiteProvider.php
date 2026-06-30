<?php

namespace DeveloperAwam\OmniCentralAuth\Http\Controllers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class OmniSocialiteProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopes = ['*'];

    protected function getAuthUrl($state): string
    {
        $serverUrl = config('omni-central-auth.client.server_url');

        return $this->buildAuthUrlFromBase("{$serverUrl}/oauth/authorize", $state);
    }

    protected function getTokenUrl(): string
    {
        $serverUrl = config('omni-central-auth.client.server_url');

        return "{$serverUrl}/oauth/token";
    }

    protected function getUserByToken($token): array
    {
        $serverUrl = config('omni-central-auth.client.server_url');

        $response = $this->getHttpClient()
            ->get("{$serverUrl}/api/user", [
                'headers' => ['Authorization' => "Bearer {$token}"],
            ]);

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user): User
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['id'],
            'name'     => $user['name'],
            'email'    => $user['email'],
            'avatar'   => $user['avatar'] ?? null,
        ]);
    }
}
