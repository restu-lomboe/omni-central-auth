<?php

use DeveloperAwam\OmniCentralAuth\Http\Livewire\Dashboard\UsersTable;
use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use Livewire\Livewire;

uses(TestCase::class);

beforeEach(function () {
    $this->admin = $this->adminUser();
    $this->regular = $this->regularUser();
});

it('renders with user list', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->assertOk()
        ->assertViewHas('users')
        ->assertSee($this->admin->name)
        ->assertSee($this->regular->name);
});

it('searches users by name', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->set('search', 'Regular')
        ->assertSee($this->regular->name)
        ->assertDontSee($this->admin->name);
});

it('searches users by email', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->set('search', 'user@test.com')
        ->assertSee($this->regular->name)
        ->assertDontSee($this->admin->name);
});

it('filters users by admin role', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->set('roleFilter', 'admin')
        ->assertSee($this->admin->name)
        ->assertDontSee($this->regular->name);
});

it('filters users by user role', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->set('roleFilter', 'user')
        ->assertSee($this->regular->name)
        ->assertDontSee($this->admin->name);
});

it('updates another user role to admin', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->call('updateRole', $this->regular->id, 'admin')
        ->assertDispatched('role-updated');

    expect($this->regular->fresh()->role)->toBe('admin');
});

it('updates another user role to user', function () {
    $anotherAdmin = $this->adminUser([
        'name' => 'Another Admin',
        'email' => 'another@test.com',
    ]);

    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->call('updateRole', $anotherAdmin->id, 'user');

    expect($anotherAdmin->fresh()->role)->toBe('user');
});

it('cannot change own role', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->call('updateRole', $this->admin->id, 'user')
        ->assertHasErrors('role');
});

it('deletes another user', function () {
    $userModel = config('omni-central-auth.user_model');
    $userId = $this->regular->id;

    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->call('deleteUser', $userId);

    expect($userModel::find($userId))->toBeNull();
});

it('cannot delete own account', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->call('deleteUser', $this->admin->id)
        ->assertHasErrors('delete');

    expect($this->admin->fresh())->not->toBeNull();
});

it('shows you badge for current user', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->assertSee('(you)');
});

it('sets search query in component', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->set('search', 'test')
        ->assertSet('search', 'test');
});

it('sets role filter in component', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->set('roleFilter', 'admin')
        ->assertSet('roleFilter', 'admin');
});

it('shows empty state when no users match filter', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->set('search', 'zzz_nonexistent_zzz')
        ->assertSee('No users found');
});

it('shows delete button for other users', function () {
    Livewire::actingAs($this->admin)
        ->test(UsersTable::class)
        ->assertSee('Delete');
});
