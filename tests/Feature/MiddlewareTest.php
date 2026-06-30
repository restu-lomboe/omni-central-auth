<?php

use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use DeveloperAwam\OmniCentralAuth\Http\Middleware\OmniAdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

uses(TestCase::class);

function runMiddleware($user = null): \Symfony\Component\HttpFoundation\Response
{
    $request = Request::create('/test');

    if ($user) {
        $request->setUserResolver(fn () => $user);
        auth()->setUser($user);
    }

    $middleware = new OmniAdminMiddleware();

    return $middleware->handle($request, fn () => new Response('OK'));
}

it('redirects unauthenticated user to login', function () {
    $response = runMiddleware(null);

    expect($response->getStatusCode())->toBe(302);
});

it('allows user with isOmniAdmin() returning true', function () {
    $user = $this->adminUser(); // has isOmniAdmin() method

    $response = runMiddleware($user);

    expect($response->getStatusCode())->toBe(200);
});

it('blocks user with isOmniAdmin() returning false', function () {
    $user = $this->regularUser();

    expect(fn () => runMiddleware($user))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

it('allows user with is_admin = true when no isOmniAdmin method', function () {
    // Anonymous class tanpa method isOmniAdmin
    $user = new class extends \Illuminate\Foundation\Auth\User {
        public $id = 1;
        public $is_admin = true;
    };

    $middleware = new OmniAdminMiddleware();
    $request = Request::create('/test');
    $request->setUserResolver(fn () => $user);

    $response = $middleware->handle($request, fn () => new Response('OK'));
    expect($response->getStatusCode())->toBe(200);
});

it('allows user with role = admin when no isOmniAdmin method and no is_admin', function () {
    $user = new class extends \Illuminate\Foundation\Auth\User {
        public $id = 1;
        public $role = 'admin';
    };

    $middleware = new OmniAdminMiddleware();
    $request = Request::create('/test');
    $request->setUserResolver(fn () => $user);

    $response = $middleware->handle($request, fn () => new Response('OK'));
    expect($response->getStatusCode())->toBe(200);
});
