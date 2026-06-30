<?php

use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use Laravel\Passport\Client;

uses(TestCase::class);

beforeEach(function () {
    $this->admin   = $this->adminUser();
});

it('admin can create a new oauth client', function () {
    $this->actingAs($this->admin)
        ->post(route('omni.dashboard.clients.store'), [
            'name'     => 'Aplikasi HR',
            'redirect' => 'https://hr.example.com/omni/callback',
        ])
        ->assertRedirect(route('omni.dashboard.clients.index'));

    $client = Client::where('name', 'Aplikasi HR')->first();
    expect($client)->not->toBeNull();
    expect(json_decode($client->getAttributes()['redirect_uris'], true)[0])
        ->toBe('https://hr.example.com/omni/callback');
});

it('create client validates required fields', function () {
    $this->actingAs($this->admin)
        ->post(route('omni.dashboard.clients.store'), [])
        ->assertSessionHasErrors(['name', 'redirect']);
});

it('create client validates redirect must be a url', function () {
    $this->actingAs($this->admin)
        ->post(route('omni.dashboard.clients.store'), [
            'name'     => 'Test App',
            'redirect' => 'not-a-url',
        ])
        ->assertSessionHasErrors(['redirect']);
});

it('admin can edit an oauth client', function () {
    $client = Client::create([
        'name'          => 'Old Name',
        'redirect_uris' => json_encode(['https://old.example.com/callback']),
        'grant_types'   => json_encode(['authorization_code', 'refresh_token']),
        'secret'        => \Illuminate\Support\Str::random(40),
        'revoked'       => false,
    ]);

    $this->actingAs($this->admin)
        ->put(route('omni.dashboard.clients.update', $client->id), [
            'name'     => 'New Name',
            'redirect' => 'https://new.example.com/callback',
        ])
        ->assertRedirect(route('omni.dashboard.clients.index'));

    $updated = Client::find($client->id);
    expect($updated->name)->toBe('New Name');
    expect($updated->redirect_uris[0])->toBe('https://new.example.com/callback'); // ← access cast array directly
});

it('admin can delete an oauth client', function () {
    $client = Client::create([
        'name'          => 'Old Name',
        'redirect_uris' => json_encode(['https://old.example.com/callback']),
        'grant_types'   => json_encode(['authorization_code', 'refresh_token']), // ← add this
        'secret'        => \Illuminate\Support\Str::random(40),
        'revoked'       => false,
    ]);

    $this->actingAs($this->admin)
        ->delete(route('omni.dashboard.clients.destroy', $client->id))
        ->assertRedirect(route('omni.dashboard.clients.index'));

    $deleted = $client->find($client->id);
    expect($deleted->revoked)->toBeTrue();
});
