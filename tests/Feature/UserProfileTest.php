<?php

use DeveloperAwam\OmniCentralAuth\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->user = $this->regularUser();
});

it('shows user dashboard page', function () {
    $this->actingAs($this->user)
        ->get(route('user.dashboard'))
        ->assertOk()
        ->assertSee($this->user->name);
});

it('blocks admin from user dashboard', function () {
    $admin = $this->adminUser();

    $this->actingAs($admin)
        ->get(route('user.dashboard'))
        ->assertForbidden();
});

it('updates user profile', function () {
    $this->actingAs($this->user)
        ->put(route('user.profile.update'), [
            'name' => 'New Name',
            'email' => $this->user->email,
        ])
        ->assertRedirect()
        ->assertSessionHas('status');

    expect($this->user->fresh()->name)->toBe('New Name');
});

it('updates user password', function () {
    $this->user->password = Hash::make('currentpassword');
    $this->user->save();

    $this->actingAs($this->user)
        ->post(route('user.password.update'), [
            'current_password' => 'currentpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])
        ->assertRedirect()
        ->assertSessionHas('status');
});

it('validates current password on password update', function () {
    $this->user->password = Hash::make('currentpassword');
    $this->user->save();

    $this->actingAs($this->user)
        ->post(route('user.password.update'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])
        ->assertSessionHasErrors(['current_password']);
});

it('redirects guest from user dashboard to login', function () {
    $this->get(route('user.dashboard'))
        ->assertRedirect(route('login'));
});
