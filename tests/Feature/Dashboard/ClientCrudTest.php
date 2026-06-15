<?php

use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use Laravel\Passport\ClientRepository;

uses(TestCase::class);

beforeEach(function () {
    $this->admin   = $this->adminUser();
    $this->clients = app(ClientRepository::class);
});

it('admin can create a new oauth client', function () {
    $this->actingAs($this->admin)
        ->post(route('omni.dashboard.clients.store'), [
            'name'     => 'Aplikasi HR',
            'redirect' => 'https://hr.example.com/omni/callback',
        ])
        ->assertRedirect(route('omni.dashboard.clients.index'));

    $client = $this->clients->all()->first(fn ($c) => $c->name === 'Aplikasi HR');

    expect($client)->not->toBeNull();
    expect($client->redirect)->toBe('https://hr.example.com/omni/callback');
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
    $client = $this->clients->create(
        userId: $this->admin->id,
        name: 'Old Name',
        redirect: 'https://old.example.com/callback',
        provider: null,
        personalAccess: false,
        password: false,
        confidential: true,
    );

    $this->actingAs($this->admin)
        ->put(route('omni.dashboard.clients.update', $client->id), [
            'name'     => 'New Name',
            'redirect' => 'https://new.example.com/callback',
        ])
        ->assertRedirect(route('omni.dashboard.clients.index'));

    $updated = $this->clients->find($client->id);
    expect($updated->name)->toBe('New Name');
    expect($updated->redirect)->toBe('https://new.example.com/callback');
});

it('admin can delete an oauth client', function () {
    $client = $this->clients->create(
        userId: $this->admin->id,
        name: 'To Be Deleted',
        redirect: 'https://delete.example.com/callback',
        provider: null,
        personalAccess: false,
        password: false,
        confidential: true,
    );

    $this->actingAs($this->admin)
        ->delete(route('omni.dashboard.clients.destroy', $client->id))
        ->assertRedirect(route('omni.dashboard.clients.index'));

    $deleted = $this->clients->find($client->id);
    expect($deleted->revoked)->toBeTrue();
});
