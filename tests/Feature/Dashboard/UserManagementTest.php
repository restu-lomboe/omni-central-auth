<?php

use DeveloperAwam\OmniCentralAuth\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->admin = $this->adminUser();
});

it('admin can update another user role to admin', function () {
    $user = $this->regularUser();

    $this->actingAs($this->admin)
        ->patch(route('omni.dashboard.users.role', $user->id), ['role' => 'admin'])
        ->assertRedirect();

    expect($user->fresh()->role)->toBe('admin');
});

it('admin cannot change their own role', function () {
    $this->actingAs($this->admin)
        ->patch(route('omni.dashboard.users.role', $this->admin->id), ['role' => 'user'])
        ->assertSessionHasErrors(['role']);
});

it('admin can delete another user', function () {
    $user = $this->regularUser();
    $userId = $user->id;

    $this->actingAs($this->admin)
        ->delete(route('omni.dashboard.users.destroy', $user->id))
        ->assertRedirect();

    $userModel = config('omni-central-auth.user_model');
    expect($userModel::find($userId))->toBeNull();
});

it('admin cannot delete themselves', function () {
    $this->actingAs($this->admin)
        ->delete(route('omni.dashboard.users.destroy', $this->admin->id))
        ->assertSessionHasErrors(['delete']);
});

it('role update validates allowed roles only', function () {
    $user = $this->regularUser();

    $this->actingAs($this->admin)
        ->patch(route('omni.dashboard.users.role', $user->id), ['role' => 'superuser'])
        ->assertSessionHasErrors(['role']);
});
