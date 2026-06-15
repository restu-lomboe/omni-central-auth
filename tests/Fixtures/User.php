<?php

namespace DeveloperAwam\OmniCentralAuth\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasApiTokens, TwoFactorAuthenticatable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_admin',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'omni_id',
        'omni_token',
        'omni_refresh_token',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['is_admin' => 'boolean'];

    public function isOmniAdmin(): bool
    {
        return $this->is_admin === true || $this->role === 'admin';
    }
}
