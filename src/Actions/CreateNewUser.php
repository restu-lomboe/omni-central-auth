<?php

namespace DeveloperAwam\OmniCentralAuth\Actions;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): mixed
    {
        Validator::make($input, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        $userModel = config('omni-central-auth.user_model');

        $isFirstUser = $userModel::count() === 0;

        return $userModel::create([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => Hash::make($input['password']),
            'role'     => $isFirstUser ? 'admin' : 'user',
            'is_admin' => $isFirstUser,
        ]);
    }
}
