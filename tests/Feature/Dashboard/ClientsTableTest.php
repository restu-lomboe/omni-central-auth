<?php

use DeveloperAwam\OmniCentralAuth\Http\Livewire\Dashboard\ClientsTable;
use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use Laravel\Passport\Client;
use Livewire\Livewire;

uses(TestCase::class);

beforeEach(function () {
    $this->admin = $this->adminUser();
});

it('renders with empty state', function () {
    Livewire::actingAs($this->admin)
        ->test(ClientsTable::class)
        ->assertOk()
        ->assertViewHas('clients')
        ->assertSee('No OAuth clients yet');
});

it('lists all clients', function () {
    $client = Client::create([
        'name' => 'Test App',
        'redirect_uris' => json_encode(['https://app.test/callback']),
        'grant_types' => json_encode(['authorization_code']),
        'secret' => 'secret123',
        'revoked' => false,
    ]);

    Livewire::actingAs($this->admin)
        ->test(ClientsTable::class)
        ->assertOk()
        ->assertSee('Test App')
        ->assertSee($client->id);
});

it('filters clients by search', function () {
    Client::create([
        'name' => 'HR Application',
        'redirect_uris' => json_encode(['https://hr.test/callback']),
        'grant_types' => json_encode(['authorization_code']),
        'secret' => 'secret123',
        'revoked' => false,
    ]);

    Client::create([
        'name' => 'Finance App',
        'redirect_uris' => json_encode(['https://finance.test/callback']),
        'grant_types' => json_encode(['authorization_code']),
        'secret' => 'secret123',
        'revoked' => false,
    ]);

    Livewire::actingAs($this->admin)
        ->test(ClientsTable::class)
        ->set('search', 'HR')
        ->assertSee('HR Application')
        ->assertDontSee('Finance App');
});

it('shows search query in component', function () {
    Livewire::actingAs($this->admin)
        ->test(ClientsTable::class)
        ->set('search', 'test')
        ->assertSet('search', 'test');
});

it('revokes a client', function () {
    $client = Client::create([
        'name' => 'To Revoke',
        'redirect_uris' => json_encode(['https://revoke.test/callback']),
        'grant_types' => json_encode(['authorization_code']),
        'secret' => 'secret123',
        'revoked' => false,
    ]);

    Livewire::actingAs($this->admin)
        ->test(ClientsTable::class)
        ->call('revokeClient', $client->id)
        ->assertDispatched('notify');

    expect($client->fresh()->revoked)->toBeTrue();
});

it('restores a revoked client', function () {
    $client = Client::create([
        'name' => 'To Restore',
        'redirect_uris' => json_encode(['https://restore.test/callback']),
        'grant_types' => json_encode(['authorization_code']),
        'secret' => 'secret123',
        'revoked' => true,
    ]);

    Livewire::actingAs($this->admin)
        ->test(ClientsTable::class)
        ->call('restoreClient', $client->id)
        ->assertDispatched('notify');

    expect($client->fresh()->revoked)->toBeFalse();
});

it('shows active status for active client', function () {
    Client::create([
        'name' => 'Active App',
        'redirect_uris' => json_encode(['https://active.test/callback']),
        'grant_types' => json_encode(['authorization_code']),
        'secret' => 'secret123',
        'revoked' => false,
    ]);

    Livewire::actingAs($this->admin)
        ->test(ClientsTable::class)
        ->assertSee('Active');
});

it('shows revoked status for revoked client', function () {
    Client::create([
        'name' => 'Inactive App',
        'redirect_uris' => json_encode(['https://inactive.test/callback']),
        'grant_types' => json_encode(['authorization_code']),
        'secret' => 'secret123',
        'revoked' => true,
    ]);

    Livewire::actingAs($this->admin)
        ->test(ClientsTable::class)
        ->assertSee('Revoked');
});

it('shows restore button for revoked client', function () {
    Client::create([
        'name' => 'Revoked App',
        'redirect_uris' => json_encode(['https://revoked.test/callback']),
        'grant_types' => json_encode(['authorization_code']),
        'secret' => 'secret123',
        'revoked' => true,
    ]);

    Livewire::actingAs($this->admin)
        ->test(ClientsTable::class)
        ->assertSee('Restore');
});

it('shows revoke button for active client', function () {
    Client::create([
        'name' => 'Active App',
        'redirect_uris' => json_encode(['https://active.test/callback']),
        'grant_types' => json_encode(['authorization_code']),
        'secret' => 'secret123',
        'revoked' => false,
    ]);

    Livewire::actingAs($this->admin)
        ->test(ClientsTable::class)
        ->assertSee('Revoke');
});
