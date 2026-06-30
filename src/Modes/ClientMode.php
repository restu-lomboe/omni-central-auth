<?php

namespace DeveloperAwam\OmniCentralAuth\Modes;

use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class ClientMode
{
    public function boot(): bool
    {
        $this->configureSocialite();

        return true;
    }

    protected function configureSocialite(): void
    {
        $socialite = app(SocialiteFactory::class);

        // Register a custom OAuth2 driver that points to the SSO Server
        $socialite->extend('omni', function () use ($socialite) {
            $config = [
                'client_id'     => config('omni-central-auth.client.client_id'),
                'client_secret' => config('omni-central-auth.client.client_secret'),
                'redirect'      => config('omni-central-auth.client.redirect_uri'),
            ];

            return $socialite->buildProvider(
                \DeveloperAwam\OmniCentralAuth\Http\Controllers\OmniSocialiteProvider::class,
                $config
            );
        });
    }
}
