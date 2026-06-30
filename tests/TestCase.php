<?php

namespace DeveloperAwam\OmniCentralAuth\Tests;

use Illuminate\Database\Schema\Blueprint;
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
        $schema = $this->app['db']->connection()->getSchemaBuilder();

        // 1. Users table
        $schema->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('omni_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('avatar')->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->string('role')->default('user');
            $table->boolean('is_admin')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Passport tables (defined inline to avoid migration loading issues)
        $schema->create('oauth_clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->nullableMorphs('owner');
            $table->string('name');
            $table->string('secret')->nullable();
            $table->string('provider')->nullable();
            $table->text('redirect_uris');
            $table->text('grant_types');
            $table->boolean('revoked');
            $table->timestamps();
        });

        $schema->create('oauth_auth_codes', function (Blueprint $table) {
            $table->char('id', 80)->primary();
            $table->foreignId('user_id')->index();
            $table->foreignUuid('client_id');
            $table->text('scopes')->nullable();
            $table->boolean('revoked');
            $table->dateTime('expires_at')->nullable();
        });

        $schema->create('oauth_access_tokens', function (Blueprint $table) {
            $table->char('id', 80)->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->foreignUuid('client_id');
            $table->string('name')->nullable();
            $table->text('scopes')->nullable();
            $table->boolean('revoked');
            $table->timestamps();
            $table->dateTime('expires_at')->nullable();
        });

        $schema->create('oauth_refresh_tokens', function (Blueprint $table) {
            $table->char('id', 80)->primary();
            $table->char('access_token_id', 80)->index();
            $table->boolean('revoked');
            $table->dateTime('expires_at')->nullable();
        });

        $schema->create('oauth_personal_access_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();
        });

        // 3. Audit log table
        $schema->create('omni_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('event');
            $table->text('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('client_app')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();
        });
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
