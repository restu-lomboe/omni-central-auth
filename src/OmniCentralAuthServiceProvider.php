<?php

namespace DeveloperAwam\OmniCentralAuth;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use DeveloperAwam\OmniCentralAuth\Console\InstallCommand;
use DeveloperAwam\OmniCentralAuth\Http\Middleware\OmniAdminMiddleware;
use DeveloperAwam\OmniCentralAuth\Http\Middleware\OmniUserMiddleware;
use DeveloperAwam\OmniCentralAuth\Modes\ServerMode;
use DeveloperAwam\OmniCentralAuth\Modes\ClientMode;

class OmniCentralAuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge config — users only need to override what they want
        $this->mergeConfigFrom(
            __DIR__ . '/../config/omni-central-auth.php',
            'omni-central-auth'
        );
    }

    public function boot(): void
    {
        $this->registerMiddleware();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerMigrations();
        $this->registerTranslations();
        $this->registerBladeComponents();
        $this->registerLivewireComponents();
        $this->registerAboutCommand();

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->registerPublishables();
        }

        $this->bootMode();
    }

    protected function bootMode(): void
    {
        $mode = config('omni-central-auth.mode');

        match ($mode) {
            'server' => (new ServerMode())->boot(),
            'client' => (new ClientMode())->boot(),
            'both'   => tap(new ServerMode(), fn ($m) => $m->boot()) && (new ClientMode())->boot(),
            default  => throw new \InvalidArgumentException(
                "Invalid omni-central-auth mode: [{$mode}]. Allowed values: server, client, both."
            ),
        };
    }

    protected function registerMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $router->aliasMiddleware('omni.admin', OmniAdminMiddleware::class);
        $router->aliasMiddleware('omni.user', OmniUserMiddleware::class);
    }

    protected function registerRoutes(): void
    {
        $mode = config('omni-central-auth.mode');

        if (in_array($mode, ['server', 'both'])) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/server.php');
        }

        if (in_array($mode, ['client', 'both'])) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/client.php');
        }

        if (config('omni-central-auth.dashboard.enabled')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/dashboard.php');
        }
    }

    protected function registerViews(): void
    {
        // Laravel 13: two locations — vendor override first, then package default
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'omni');
    }

    protected function registerMigrations(): void
    {
        if (config('omni-central-auth.load_migrations', true)) {
            // Laravel 13: publishesMigrations automatically updates timestamp on publish
            $this->publishesMigrations([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'omni-migrations');
        }
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'omni');
    }

    protected function registerBladeComponents(): void
    {
        Blade::anonymousComponentPath(
            path: __DIR__ . '/../resources/views/components',
            prefix: 'omni'
        );
    }

    protected function registerLivewireComponents(): void
    {
        if (! class_exists(\Livewire\Livewire::class)) {
            return;
        }

        \Livewire\Livewire::addNamespace(
            namespace: 'omni',
            classNamespace: 'DeveloperAwam\\OmniCentralAuth\\Http\\Livewire',
            classPath: __DIR__ . '/Http/Livewire',
            classViewPath: __DIR__ . '/../resources/views/livewire',
        );
    }

    protected function registerAboutCommand(): void
    {
        // Shows when developer runs `php artisan about`
        AboutCommand::add('Omni Central Auth', fn () => [
            'Version' => '1.0.0',
            'Mode'    => config('omni-central-auth.mode'),
            'Dashboard' => config('omni-central-auth.dashboard.enabled') ? 'Enabled' : 'Disabled',
        ]);
    }

    protected function registerCommands(): void
    {
        $this->commands([
            InstallCommand::class,
        ]);

        // Register with optimize to cache config when running `php artisan optimize`
        // $this->optimizes(
        //     optimize: 'omni:optimize',
        //     clear: 'omni:clear',
        // );
    }

    protected function registerPublishables(): void
    {
        // Config
        $this->publishes([
            __DIR__ . '/../config/omni-central-auth.php' => config_path('omni-central-auth.php'),
        ], 'omni-config');

        // Views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/omni'),
        ], 'omni-views');

        // Language files
        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/omni'),
        ], 'omni-lang');

        // All at once
        $this->publishes([
            __DIR__ . '/../config/omni-central-auth.php' => config_path('omni-central-auth.php'),
            __DIR__ . '/../resources/views'              => resource_path('views/vendor/omni'),
            __DIR__ . '/../lang'                         => $this->app->langPath('vendor/omni'),
        ], 'omni-all');
    }
}
