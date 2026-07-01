<?php

use DeveloperAwam\OmniCentralAuth\Actions\CreateNewUser;
use DeveloperAwam\OmniCentralAuth\Actions\ResetUserPassword;
use DeveloperAwam\OmniCentralAuth\Actions\UpdateUserPassword;
use DeveloperAwam\OmniCentralAuth\Actions\UpdateUserProfileInformation;
use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

uses(TestCase::class);

it('creates first user as admin', function () {
    $action = new CreateNewUser;

    $user = $action->create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    expect($user->name)->toBe('Admin User');
    expect($user->role)->toBe('admin');
    expect($user->is_admin)->toBeTrue();
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

it('creates subsequent users as regular user', function () {
    $this->adminUser();

    $action = new CreateNewUser;

    $user = $action->create([
        'name' => 'Regular User',
        'email' => 'user@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    expect($user->role)->toBe('user');
    expect($user->is_admin)->toBeFalse();
});

it('validates required fields on create', function () {
    $action = new CreateNewUser;

    $action->create(['name' => '', 'email' => 'not-email', 'password' => 'short']);
})->throws(ValidationException::class);

it('resets user password', function () {
    $user = $this->regularUser();
    $action = new ResetUserPassword;

    $action->reset($user, [
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
});

it('validates password on reset', function () {
    $user = $this->regularUser();
    $action = new ResetUserPassword;

    $action->reset($user, ['password' => 'short', 'password_confirmation' => 'short']);
})->throws(ValidationException::class);

it('updates user password with current password', function () {
    $user = $this->regularUser();
    $user->password = Hash::make('currentpassword');
    $user->save();

    $this->actingAs($user);

    $action = new UpdateUserPassword;
    $action->update($user, [
        'current_password' => 'currentpassword',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
});

it('validates current password on update', function () {
    $user = $this->regularUser();
    $user->password = Hash::make('currentpassword');
    $user->save();

    $this->actingAs($user);

    $action = new UpdateUserPassword;
    $action->update($user, [
        'current_password' => 'wrongpassword',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);
})->throws(ValidationException::class);

it('updates user profile information', function () {
    $user = $this->regularUser();
    $action = new UpdateUserProfileInformation;

    $action->update($user, [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    $fresh = $user->fresh();
    expect($fresh->name)->toBe('Updated Name');
    expect($fresh->email)->toBe('updated@example.com');
});

it('validates unique email on profile update when email changes', function () {
    $this->adminUser(['email' => 'existing@example.com']);
    $user = $this->regularUser();
    $action = new UpdateUserProfileInformation;

    $action->update($user, [
        'name' => 'Test',
        'email' => 'existing@example.com',
    ]);
})->throws(ValidationException::class);

it('does not require unique email when email unchanged', function () {
    $user = $this->regularUser(['email' => 'same@example.com']);
    $action = new UpdateUserProfileInformation;

    $action->update($user, [
        'name' => 'Test',
        'email' => 'same@example.com',
    ]);

    expect($user->fresh()->name)->toBe('Test');
});

it('uploads avatar on profile update', function () {
    Storage::fake('public');

    $user = $this->regularUser();
    $action = new UpdateUserProfileInformation;

    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

    $action->update($user, [
        'name' => 'With Avatar',
        'email' => $user->email,
        'avatar' => $file,
    ]);

    $fresh = $user->fresh();
    expect($fresh->avatar)->not->toBeNull();
    Storage::disk('public')->assertExists($fresh->avatar);
});

it('deletes old avatar when new one uploaded', function () {
    $user = $this->regularUser();
    Storage::fake('public');

    $oldPath = 'avatars/old-avatar.jpg';
    Storage::disk('public')->put($oldPath, 'fake-content');
    $user->avatar = $oldPath;
    $user->save();

    $action = new UpdateUserProfileInformation;
    $file = UploadedFile::fake()->image('new-avatar.jpg', 100, 100);

    $action->update($user, [
        'name' => 'Updated',
        'email' => $user->email,
        'avatar' => $file,
    ]);

    Storage::disk('public')->assertMissing($oldPath);
});

it('validates avatar file type', function () {
    $user = $this->regularUser();
    $action = new UpdateUserProfileInformation;

    $file = UploadedFile::fake()->create('document.pdf', 100);

    $action->update($user, [
        'name' => 'Test',
        'email' => $user->email,
        'avatar' => $file,
    ]);
})->throws(ValidationException::class);
