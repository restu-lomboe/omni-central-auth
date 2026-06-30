<?php

use DeveloperAwam\OmniCentralAuth\Tests\TestCase;

uses(TestCase::class);

it('registers omni config', function () {
    expect(config('omni-central-auth'))->toBeArray();
    expect(config('omni-central-auth.mode'))->toBe('server');
});

it('registers middleware alias omni.admin', function () {
    $router = app('router');
    $middleware = $router->getMiddleware();

    expect($middleware)->toHaveKey('omni.admin');
    expect($middleware['omni.admin'])
        ->toBe(\DeveloperAwam\OmniCentralAuth\Http\Middleware\OmniAdminMiddleware::class);
});

it('registers dashboard routes when dashboard enabled', function () {
    $routes = collect(app('router')->getRoutes()->getRoutes())
        ->map(fn ($r) => $r->getName())
        ->filter()
        ->values();

    expect($routes)->toContain('omni.dashboard.index');
    expect($routes)->toContain('omni.dashboard.clients.index');
    expect($routes)->toContain('omni.dashboard.users.index');
    expect($routes)->toContain('omni.dashboard.audit-log.index');
});

it('registers server routes in server mode', function () {
    $routes = collect(app('router')->getRoutes()->getRoutes())
        ->map(fn ($r) => $r->getName())
        ->filter()
        ->values();

    expect($routes)->toContain('omni.authorize');
});

it('throws exception for invalid mode', function () {
    config(['omni-central-auth.mode' => 'invalid']);

    $this->expectException(\InvalidArgumentException::class);

    (new \DeveloperAwam\OmniCentralAuth\OmniCentralAuthServiceProvider(app()))->boot();
});
