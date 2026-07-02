<?php

use DeveloperAwam\OmniCentralAuth\Http\Controllers\OmniSocialiteProvider;
use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use Illuminate\Http\Request;

uses(TestCase::class);

it('builds token url from server url config', function () {
    config(['omni-central-auth.client.server_url' => 'https://sso.example.com']);

    $request = Request::create('/omni/login');
    $request->setLaravelSession(app('session')->driver());

    $provider = new OmniSocialiteProvider(
        $request,
        'test-client-id',
        'test-secret',
        'https://app.example.com/callback',
    );

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('getTokenUrl');
    $method->setAccessible(true);

    $url = $method->invoke($provider);

    expect($url)->toBe('https://sso.example.com/oauth/token');
});

it('maps user with avatar from sso response', function () {
    config(['omni-central-auth.client.server_url' => 'https://sso.example.com']);

    $request = Request::create('/omni/login');
    $request->setLaravelSession(app('session')->driver());

    $provider = new OmniSocialiteProvider($request, 'id', 'secret', 'https://app.example.com/callback');

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('mapUserToObject');
    $method->setAccessible(true);

    $user = $method->invoke($provider, [
        'id' => '1',
        'name' => 'Test User',
        'email' => 'test@example.com',
        'avatar' => 'https://example.com/avatar.jpg',
    ]);

    expect($user->getAvatar())->toBe('https://example.com/avatar.jpg');
});

it('maps user without avatar', function () {
    config(['omni-central-auth.client.server_url' => 'https://sso.example.com']);

    $request = Request::create('/omni/login');
    $request->setLaravelSession(app('session')->driver());

    $provider = new OmniSocialiteProvider($request, 'id', 'secret', 'https://app.example.com/callback');

    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('mapUserToObject');
    $method->setAccessible(true);

    $user = $method->invoke($provider, [
        'id' => '1',
        'name' => 'No Avatar',
        'email' => 'noavatar@example.com',
    ]);

    expect($user->getAvatar())->toBeNull();
});
