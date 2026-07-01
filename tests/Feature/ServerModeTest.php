<?php

use DeveloperAwam\OmniCentralAuth\Modes\ServerMode;
use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;

uses(TestCase::class);

it('boots server mode successfully', function () {
    $mode = new ServerMode;
    $result = $mode->boot();

    expect($result)->toBeTrue();
});

it('renders login page', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Email');
});

it('registers login response contract for role-based redirect', function () {
    $loginResponse = app(LoginResponse::class);
    expect($loginResponse)->not->toBeNull();

    $admin = $this->adminUser();
    $request = Request::create('/login');
    $request->setUserResolver(fn () => $admin);

    $response = $loginResponse->toResponse($request);
    expect($response->getStatusCode())->toBe(302);
    expect($response->headers->get('Location'))->toContain('omni-dashboard');
});

it('redirects regular user to /user via login response', function () {
    $loginResponse = app(LoginResponse::class);

    $user = $this->regularUser();
    $request = Request::create('/login');
    $request->setUserResolver(fn () => $user);

    $response = $loginResponse->toResponse($request);
    expect($response->headers->get('Location'))->toEndWith('/user');
});

it('redirects admin to omni-dashboard via register response', function () {
    $registerResponse = app(RegisterResponse::class);
    expect($registerResponse)->not->toBeNull();

    $admin = $this->adminUser();
    $request = Request::create('/register');
    $request->setUserResolver(fn () => $admin);

    $response = $registerResponse->toResponse($request);
    expect($response->getStatusCode())->toBe(302);
    expect($response->headers->get('Location'))->toContain('omni-dashboard');
});

it('redirects regular user to /user via register response', function () {
    $registerResponse = app(RegisterResponse::class);

    $user = $this->regularUser();
    $request = Request::create('/register');
    $request->setUserResolver(fn () => $user);

    $response = $registerResponse->toResponse($request);
    expect($response->headers->get('Location'))->toEndWith('/user');
});

it('uses custom dashboard prefix for admin redirect', function () {
    config(['omni-central-auth.dashboard.prefix' => 'custom-admin']);

    $mode = new ServerMode;
    $mode->boot();

    $loginResponse = app(LoginResponse::class);

    $admin = $this->adminUser();
    $request = Request::create('/login');
    $request->setUserResolver(fn () => $admin);

    $response = $loginResponse->toResponse($request);
    expect($response->headers->get('Location'))->toContain('custom-admin');
});
