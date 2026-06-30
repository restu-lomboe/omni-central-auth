<?php

use DeveloperAwam\OmniCentralAuth\Tests\TestCase;

uses(TestCase::class);

$prefix = 'omni-dashboard';

it('redirects guest to login when accessing dashboard', function () use ($prefix) {
    $this->get("/{$prefix}")
        ->assertRedirect('/login');
});

it('blocks non-admin from accessing dashboard', function () use ($prefix) {
    $user = $this->regularUser();

    $this->actingAs($user)
        ->get("/{$prefix}")
        ->assertForbidden();
});

it('allows admin to access dashboard overview', function () use ($prefix) {
    $admin = $this->adminUser();

    $this->actingAs($admin)
        ->get("/{$prefix}")
        ->assertOk();
});

it('allows admin to access clients page', function () use ($prefix) {
    $admin = $this->adminUser();

    $this->actingAs($admin)
        ->get("/{$prefix}/clients")
        ->assertOk();
});

it('allows admin to access users page', function () use ($prefix) {
    $admin = $this->adminUser();

    $this->actingAs($admin)
        ->get("/{$prefix}/users")
        ->assertOk();
});

it('allows admin to access audit log page', function () use ($prefix) {
    $admin = $this->adminUser();

    $this->actingAs($admin)
        ->get("/{$prefix}/audit-log")
        ->assertOk();
});

it('allows admin to access create client form', function () use ($prefix) {
    $admin = $this->adminUser();

    $this->actingAs($admin)
        ->get("/{$prefix}/clients/create")
        ->assertOk();
});
