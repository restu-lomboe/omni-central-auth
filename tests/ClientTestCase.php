<?php

namespace DeveloperAwam\OmniCentralAuth\Tests;

use Livewire\LivewireServiceProvider;

abstract class ClientTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            \Laravel\Fortify\FortifyServiceProvider::class,
            \Laravel\Socialite\SocialiteServiceProvider::class,
            LivewireServiceProvider::class,
            \DeveloperAwam\OmniCentralAuth\OmniCentralAuthServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('omni-central-auth.mode', 'client');
        $app['config']->set('omni-central-auth.client.server_url', 'https://sso.example.com');
        $app['config']->set('omni-central-auth.client.client_id', 'test-client-id');
        $app['config']->set('omni-central-auth.client.client_secret', 'test-secret');
        $app['config']->set('omni-central-auth.client.redirect_uri', 'https://app.example.com/omni/callback');
        $app['config']->set('omni-central-auth.client.signing_key', '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef');
        $app['config']->set('omni-central-auth.client.home_url', '/dashboard');
        $app['config']->set('omni-central-auth.dashboard.enabled', false);
    }
}
