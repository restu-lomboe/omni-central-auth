<?php

use DeveloperAwam\OmniCentralAuth\Http\Controllers\Server\AuthorizationController;
use DeveloperAwam\OmniCentralAuth\Models\AuditLog;
use DeveloperAwam\OmniCentralAuth\Tests\ClientTestCase;

uses(ClientTestCase::class);

it('redirects to sso server for login', function () {
    $this->get(route('omni.login'))
        ->assertRedirect();
});

it('handles callback with valid sso data', function () {
    $payload = AuthorizationController::encryptPayload([
        'omni_id' => 99,
        'name' => 'SSO User',
        'email' => 'sso@example.com',
        'avatar' => null,
        'timestamp' => now()->timestamp,
    ], config('omni-central-auth.client.signing_key'));

    $this->get(route('omni.callback', ['sso_data' => $payload]))
        ->assertRedirect(config('omni-central-auth.client.home_url'));

    $userModel = config('omni-central-auth.user_model');
    $user = $userModel::where('email', 'sso@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('SSO User');
    expect($user->omni_id)->toBe('99');

    expect(AuditLog::where('event', 'login')->count())->toBe(1);
});

it('returns error when sso_data missing', function () {
    $this->get(route('omni.callback'))
        ->assertRedirect()
        ->assertSessionHasErrors(['sso']);
});

it('returns error when signing key not configured', function () {
    config(['omni-central-auth.client.signing_key' => '']);

    $this->get(route('omni.callback', ['sso_data' => 'somedata']))
        ->assertRedirect()
        ->assertSessionHasErrors(['sso']);
});

it('returns error when payload is invalid', function () {
    $this->get(route('omni.callback', ['sso_data' => base64_encode('invalid-data')]))
        ->assertRedirect()
        ->assertSessionHasErrors(['sso']);
});

it('logs out and redirects to sso server', function () {
    $user = $this->regularUser();
    $this->actingAs($user);

    $this->post(route('omni.logout'))
        ->assertRedirect('https://sso.example.com/logout');

    expect(AuditLog::where('event', 'logout')->count())->toBe(1);
    expect(auth()->check())->toBeFalse();
});
