<?php

use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use DeveloperAwam\OmniCentralAuth\Http\Controllers\OmniSocialiteProvider;

uses(TestCase::class);

it('builds correct authorize url pointing to sso server', function () {
    config([
        'omni-central-auth.client.server_url'    => 'https://sso.example.com',
        'omni-central-auth.client.client_id'     => 'test-client-id',
        'omni-central-auth.client.client_secret' => 'test-secret',
        'omni-central-auth.client.redirect_uri'  => 'https://app.example.com/omni/callback',
    ]);

    $request = \Illuminate\Http\Request::create('/omni/login');
    $request->setLaravelSession(app('session')->driver());

    $provider = new OmniSocialiteProvider(
        $request,
        'test-client-id',
        'test-secret',
        'https://app.example.com/omni/callback',
    );

    $redirectUrl = $provider->redirect()->getTargetUrl();

    expect($redirectUrl)->toContain('https://sso.example.com/oauth/authorize');
    expect($redirectUrl)->toContain('client_id=test-client-id');
    expect($redirectUrl)->toContain('response_type=code');
});

it('maps sso user object correctly', function () {
    config(['omni-central-auth.client.server_url' => 'https://sso.example.com']);

    $request = \Illuminate\Http\Request::create('/omni/login');
    $request->setLaravelSession(app('session')->driver());

    $provider = new OmniSocialiteProvider($request, 'id', 'secret', 'https://app.example.com/callback');

    $reflection = new \ReflectionClass($provider);
    $method = $reflection->getMethod('mapUserToObject');
    $method->setAccessible(true);

    $user = $method->invoke($provider, [
        'id'    => '42',
        'name'  => 'Budi Santoso',
        'email' => 'budi@example.com',
    ]);

    expect($user->getId())->toBe('42');
    expect($user->getName())->toBe('Budi Santoso');
    expect($user->getEmail())->toBe('budi@example.com');
});
