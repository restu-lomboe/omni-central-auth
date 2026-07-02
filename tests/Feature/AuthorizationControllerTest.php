<?php

use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use Illuminate\Support\Str;
use Laravel\Passport\Client;

uses(TestCase::class);

beforeEach(function () {
    config(['omni-central-auth.server.signing_key' => '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef']);
});

it('shows authorize page for valid client', function () {
    $client = Client::create([
        'id' => (string) Str::orderedUuid(),
        'name' => 'Test Client',
        'redirect_uris' => json_encode(['https://app.example.com/callback']),
        'grant_types' => json_encode(['authorization_code', 'refresh_token']),
        'secret' => Str::random(40),
        'revoked' => false,
    ]);

    $user = $this->regularUser();
    $this->actingAs($user)
        ->get(route('omni.authorize', ['client_id' => $client->id]))
        ->assertOk()
        ->assertSee('Test Client');
});

it('returns 400 for unknown client on authorize page', function () {
    $user = $this->regularUser();
    $this->actingAs($user)
        ->get(route('omni.authorize', ['client_id' => 'non-existent']))
        ->assertStatus(400);
});

it('returns 400 for invalid client on approve', function () {
    $user = $this->regularUser();

    $this->actingAs($user)
        ->post(route('omni.authorize.approve'), ['client_id' => 'non-existent'])
        ->assertStatus(400);
});

it('returns 500 when signing key not configured on approve', function () {
    config(['omni-central-auth.server.signing_key' => '']);

    $client = Client::create([
        'id' => (string) Str::orderedUuid(),
        'name' => 'Test Client',
        'redirect_uris' => json_encode(['https://app.example.com/callback']),
        'grant_types' => json_encode(['authorization_code', 'refresh_token']),
        'secret' => Str::random(40),
        'revoked' => false,
    ]);

    $user = $this->regularUser();
    $this->actingAs($user)
        ->post(route('omni.authorize.approve'), ['client_id' => $client->id])
        ->assertStatus(500);
});

it('approves and returns approved view with sso data', function () {
    $client = Client::create([
        'id' => (string) Str::orderedUuid(),
        'name' => 'Test Client',
        'redirect_uris' => json_encode(['https://app.example.com/callback']),
        'grant_types' => json_encode(['authorization_code', 'refresh_token']),
        'secret' => Str::random(40),
        'revoked' => false,
    ]);

    $user = $this->adminUser();
    $this->actingAs($user)
        ->post(route('omni.authorize.approve'), ['client_id' => $client->id])
        ->assertOk()
        ->assertSee('sso_data');
});

it('handles redirect_uris as scalar in raw attributes', function () {
    $client = new Client([
        'id' => (string) Str::orderedUuid(),
        'name' => 'Test',
        'redirect_uris' => json_encode(['https://example.com/callback']),
        'grant_types' => json_encode(['authorization_code', 'refresh_token']),
        'secret' => Str::random(40),
        'revoked' => false,
    ]);
    $client->save();

    // Force raw attribute to be a scalar string
    $client->setRawAttributes([
        'id' => $client->id,
        'redirect_uris' => 'https://example.com/callback',
    ], false);

    $user = $this->regularUser();
    $this->actingAs($user)
        ->post(route('omni.authorize.approve'), ['client_id' => $client->id])
        ->assertOk();
});

it('denies authorization and shows denied view', function () {
    $this->delete(route('omni.authorize.deny'))
        ->assertOk();
});
