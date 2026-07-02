<?php

namespace DeveloperAwam\OmniCentralAuth\Tests;

use DeveloperAwam\OmniCentralAuth\OmniCentralAuthServiceProvider;
use Laravel\Fortify\FortifyServiceProvider;
use Laravel\Socialite\SocialiteServiceProvider;
use Livewire\LivewireServiceProvider;

abstract class ClientTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            FortifyServiceProvider::class,
            SocialiteServiceProvider::class,
            LivewireServiceProvider::class,
            OmniCentralAuthServiceProvider::class,
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
