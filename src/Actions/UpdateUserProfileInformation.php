<?php

namespace DeveloperAwam\OmniCentralAuth\Actions;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(mixed $user, array $input): void
    {
        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ];

        // Only require unique email if it changed
        if ($input['email'] !== $user->email) {
            $rules['email'][] = 'unique:users';
        }

        if (isset($input['avatar']) && $input['avatar']) {
            $rules['avatar'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'];
        }

        Validator::make($input, $rules)->validate();

        // Handle avatar upload
        if (isset($input['avatar']) && $input['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $input['avatar']->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->forceFill([
            'name'  => $input['name'],
            'email' => $input['email'],
        ])->save();
    }
}
