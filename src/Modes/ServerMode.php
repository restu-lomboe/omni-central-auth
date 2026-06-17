<?php

namespace DeveloperAwam\OmniCentralAuth\Modes;

use Laravel\Fortify\Fortify;
use Laravel\Passport\Passport;
use DeveloperAwam\OmniCentralAuth\Actions\CreateNewUser;
use DeveloperAwam\OmniCentralAuth\Actions\ResetUserPassword;
use DeveloperAwam\OmniCentralAuth\Actions\UpdateUserPassword;

class ServerMode
{
    public function boot(): bool
    {
        $this->configurePassport();
        $this->configureFortify();
        $this->registerFortifyActions();

        return true;
    }

    protected function configurePassport(): void
    {
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        Passport::authorizationView('omni::server.authorize');
    }

    protected function configureFortify(): void
    {
        Fortify::loginView(fn () => view('omni::auth.login'));
        Fortify::registerView(fn () => view('omni::auth.register'));
        Fortify::requestPasswordResetLinkView(fn () => view('omni::auth.forgot-password'));
        Fortify::resetPasswordView(fn ($request) => view('omni::auth.reset-password', ['request' => $request]));

        if (config('omni-central-auth.server.two_factor_auth')) {
            Fortify::twoFactorChallengeView(fn () => view('omni::auth.two-factor-challenge'));
        }
    }

    protected function registerFortifyActions(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
    }
}
