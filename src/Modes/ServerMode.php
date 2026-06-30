<?php

namespace DeveloperAwam\OmniCentralAuth\Modes;

use Laravel\Fortify\Fortify;
use Laravel\Passport\Passport;
use DeveloperAwam\OmniCentralAuth\Actions\CreateNewUser;
use DeveloperAwam\OmniCentralAuth\Actions\ResetUserPassword;
use DeveloperAwam\OmniCentralAuth\Actions\UpdateUserPassword;
use DeveloperAwam\OmniCentralAuth\Actions\UpdateUserProfileInformation;

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
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);

        $this->registerRedirectResponses();
    }

    protected function registerRedirectResponses(): void
    {
        $redirect = function ($request) {
            $user = $request->user();

            $isAdmin = match (true) {
                method_exists($user, 'isOmniAdmin') => $user->isOmniAdmin(),
                isset($user->is_admin)              => (bool) $user->is_admin,
                isset($user->role)                  => in_array($user->role, ['admin', 'super_admin']),
                default                             => false,
            };

            $prefix = config('omni-central-auth.dashboard.prefix', 'omni-dashboard');

            return redirect($isAdmin ? "/{$prefix}" : '/user');
        };

        app()->singleton(\Laravel\Fortify\Contracts\LoginResponse::class, function () use ($redirect) {
            return new class($redirect) implements \Laravel\Fortify\Contracts\LoginResponse {
                public function __construct(private $redirect) {}
                public function toResponse($request): \Symfony\Component\HttpFoundation\Response
                {
                    return ($this->redirect)($request);
                }
            };
        });

        app()->singleton(\Laravel\Fortify\Contracts\RegisterResponse::class, function () use ($redirect) {
            return new class($redirect) implements \Laravel\Fortify\Contracts\RegisterResponse {
                public function __construct(private $redirect) {}
                public function toResponse($request): \Symfony\Component\HttpFoundation\Response
                {
                    return ($this->redirect)($request);
                }
            };
        });
    }
}
