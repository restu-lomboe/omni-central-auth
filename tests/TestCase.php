<?php

namespace DeveloperAwam\OmniCentralAuth\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use DeveloperAwam\OmniCentralAuth\OmniCentralAuthServiceProvider;
use Laravel\Passport\PassportServiceProvider;
use Laravel\Fortify\FortifyServiceProvider;
use Livewire\LivewireServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsForTests();
    }

    protected function getPackageProviders($app): array
    {
        return [
            PassportServiceProvider::class,
            FortifyServiceProvider::class,
            LivewireServiceProvider::class,
            OmniCentralAuthServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        // Database in-memory untuk testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Auth config
        $app['config']->set('auth.guards.api.driver', 'passport');
        $app['config']->set('auth.providers.users.model', \DeveloperAwam\OmniCentralAuth\Tests\Fixtures\User::class);

        // Package config
        $app['config']->set('omni-central-auth.mode', 'server');
        $app['config']->set('omni-central-auth.user_model', \DeveloperAwam\OmniCentralAuth\Tests\Fixtures\User::class);
        $app['config']->set('omni-central-auth.dashboard.enabled', true);
    }

    protected function loadMigrationsForTests(): void
    {
        // Users table minimal untuk testing
        $this->app['db']->connection()->getSchemaBuilder()->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('role')->default('user');
            $table->boolean('is_admin')->default(false);
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->string('omni_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

            // 2. Load Passport migrations (oauth_clients, oauth_tokens, dll)
        $this->loadMigrationsFrom(
            __DIR__ . '/../../vendor/laravel/passport/database/migrations'
        );

        // 3. Load hanya audit_logs migration dari package
        $this->loadMigrationsFrom(
            __DIR__ . '/../database/migrations/2024_01_01_000001_create_omni_audit_logs_table.php'
        );
    }

    /**
     * Buat admin user untuk testing dashboard.
     */
    protected function adminUser(): \DeveloperAwam\OmniCentralAuth\Tests\Fixtures\User
    {
        return \DeveloperAwam\OmniCentralAuth\Tests\Fixtures\User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'is_admin' => true,
        ]);
    }

    /**
     * Buat regular user untuk testing.
     */
    protected function regularUser(): \DeveloperAwam\OmniCentralAuth\Tests\Fixtures\User
    {
        return \DeveloperAwam\OmniCentralAuth\Tests\Fixtures\User::create([
            'name'     => 'Regular User',
            'email'    => 'user@test.com',
            'password' => bcrypt('password'),
            'role'     => 'user',
            'is_admin' => false,
        ]);
    }
}
