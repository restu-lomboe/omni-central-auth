<?php

use DeveloperAwam\OmniCentralAuth\Http\Middleware\OmniUserMiddleware;
use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(TestCase::class);

function runUserMiddleware($user = null): Symfony\Component\HttpFoundation\Response
{
    $request = Request::create('/test');

    if ($user) {
        $request->setUserResolver(fn () => $user);
        auth()->setUser($user);
    }

    $middleware = new OmniUserMiddleware;

    return $middleware->handle($request, fn () => new Response('OK'));
}

it('redirects unauthenticated user to login', function () {
    $response = runUserMiddleware(null);

    expect($response->getStatusCode())->toBe(302);
});

it('allows user with role = user', function () {
    $user = $this->regularUser();

    $response = runUserMiddleware($user);

    expect($response->getStatusCode())->toBe(200);
});

it('blocks admin user', function () {
    $user = $this->adminUser();

    expect(fn () => runUserMiddleware($user))->toThrow(HttpException::class);
});

it('allows user with isOmniUser() returning true', function () {
    $user = new class extends User
    {
        public $id = 1;

        public $role = 'user';

        public function isOmniUser(): bool
        {
            return true;
        }
    };

    $middleware = new OmniUserMiddleware;
    $request = Request::create('/test');
    $request->setUserResolver(fn () => $user);

    $response = $middleware->handle($request, fn () => new Response('OK'));
    expect($response->getStatusCode())->toBe(200);
});

it('blocks user with isOmniUser() returning false', function () {
    $user = new class extends User
    {
        public $id = 1;

        public $role = 'admin';

        public function isOmniUser(): bool
        {
            return false;
        }
    };

    $middleware = new OmniUserMiddleware;
    $request = Request::create('/test');
    $request->setUserResolver(fn () => $user);

    expect(fn () => $middleware->handle($request, fn () => new Response('OK')))
        ->toThrow(HttpException::class);
});

it('allows user when role is not set and is_admin is false', function () {
    $user = new class extends User
    {
        public $id = 1;

        public $is_admin = false;
    };

    $middleware = new OmniUserMiddleware;
    $request = Request::create('/test');
    $request->setUserResolver(fn () => $user);

    $response = $middleware->handle($request, fn () => new Response('OK'));
    expect($response->getStatusCode())->toBe(200);
});

it('blocks user when role is not set and is_admin is true', function () {
    $user = new class extends User
    {
        public $id = 1;

        public $is_admin = true;
    };

    $middleware = new OmniUserMiddleware;
    $request = Request::create('/test');
    $request->setUserResolver(fn () => $user);

    expect(fn () => $middleware->handle($request, fn () => new Response('OK')))
        ->toThrow(HttpException::class);
});
