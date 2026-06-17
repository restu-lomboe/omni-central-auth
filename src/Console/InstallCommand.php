<?php

namespace DeveloperAwam\OmniCentralAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InstallCommand extends Command
{
    protected bool $adminCreated = false;

    protected $signature = 'omni:install
                            {--mode= : Installation mode: server, client, or both}
                            {--force : Overwrite existing files}';

    protected $description = 'Install and setup Omni Central Auth package';

    public function handle(): int
    {
        $this->displayBanner();

        $mode = $this->option('mode') ?? $this->askMode();

        $this->info("⚙️  Selected mode: [{$mode}]");
        $this->newLine();

        // Publish config
        $this->publishConfig();

        // Publish migrations
        $this->publishMigrations();

        // Publish views
        if ($this->confirm('Publish views for customization?', false)) {
            $this->publishViews();
        }

        // Mode-specific setup
        match ($mode) {
            'server' => $this->setupServer(),
            'client' => $this->setupClient(),
            'both'   => $this->setupServer() & $this->setupClient(),
        };

        // Set env
        $this->setEnvVariable('OMNI_AUTH_MODE', $mode);

        // Create admin user
        if (in_array($mode, ['server', 'both'])) {
            $this->newLine();
            $this->createAdminUser();
        }

        $this->newLine();
        $this->displaySuccess($mode);

        return self::SUCCESS;
    }

    protected function askMode(): string
    {
        return $this->choice(
            "What is this application's role?",
            [
                'server' => 'server — This app is the SSO Server (Identity Provider)',
                'client' => 'client — This app is a Client App (Service Provider)',
                'both'   => 'both   — Both (for development)',
            ],
            'server'
        );
    }

    protected function publishConfig(): void
    {
        $this->callSilently('vendor:publish', [
            '--tag'   => 'omni-config',
            '--force' => $this->option('force'),
        ]);
        $this->line('  <fg=green>✔</> Config published to <fg=cyan>config/omni-central-auth.php</>');
    }

    protected function publishMigrations(): void
    {
        $this->callSilently('vendor:publish', [
            '--tag'   => 'omni-migrations',
            '--force' => $this->option('force'),
        ]);
        $this->line('  <fg=green>✔</> Migrations published to <fg=cyan>database/migrations/</>');
    }

    protected function publishViews(): void
    {
        $this->callSilently('vendor:publish', [
            '--tag'   => 'omni-views',
            '--force' => $this->option('force'),
        ]);
        $this->line('  <fg=green>✔</> Views published to <fg=cyan>resources/views/vendor/omni/</>');
    }

    protected function setupServer(): void
    {
        $this->newLine();
        $this->line('  <fg=yellow>SERVER MODE SETUP</>');

        // Install Passport
        $this->call('passport:install');
        $this->line('  <fg=green>✔</> Passport keys & clients created');

        // Reminder
        $this->line('  <fg=yellow>!</> Add <fg=cyan>HasApiTokens</> trait to your User model');
        $this->line('  <fg=yellow>!</> Add <fg=cyan>TwoFactorAuthenticatable</> trait for 2FA');
    }

    protected function setupClient(): void
    {
        $this->newLine();
        $this->line('  <fg=yellow>CLIENT MODE SETUP</>');

        $this->line('  <fg=yellow>!</> Set the following variables in your <fg=cyan>.env</> file:');
        $this->line('       OMNI_CLIENT_SERVER_URL=https://your-sso-server.com');
        $this->line('       OMNI_CLIENT_ID=your-client-id');
        $this->line('       OMNI_CLIENT_SECRET=your-client-secret');
    }

    protected function setEnvVariable(string $key, string $value): void
    {
        $path = base_path('.env');

        if (! file_exists($path)) {
            return;
        }

        $content = file_get_contents($path);

        if (str_contains($content, $key . '=')) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}\n";
        }

        file_put_contents($path, $content);
        $this->line("  <fg=green>✔</> .env updated: <fg=cyan>{$key}={$value}</>");
    }

    protected function displayBanner(): void
    {
        $this->newLine();
        $this->line('  <fg=cyan>
   ___                 _    ___         _             _    _         _   _
  / _ \ _ __ ___  _ _ (_)  / __|___ _ _| |_ _ _ __ _| |  /_\ _  _| |_| |_
 | (_) | \'_ \/ -_)| \' \| | | (__/ -_) \'_|  _| \'_/ _` | | / _ \ || |  _| \' \\
  \___/| .__/\___||_||_|_|  \___\___|_|  \__|_| \__,_| |/_/ \_\_,_|\__|_||_|
       |_|
  </>');
        $this->line('  <fg=gray>developerawam/omni-central-auth — SSO for Laravel</>');
        $this->newLine();
    }

    protected function createAdminUser(): void
    {
        if (! $this->confirm('Create an admin user now? (to log in immediately)', true)) {
            $this->line('  <fg=yellow>!</> Skipping admin user creation. Run <fg=cyan>php artisan migrate</> first, then register the first user on the register page.');

            return;
        }

        $this->line('  <fg=yellow>Running migrations first...</>');
        $this->call('migrate');

        $userModel = config('omni-central-auth.user_model');

        if ($userModel::count() > 0) {
            $this->warn('  Users already exist in the database. Skipping admin creation.');

            return;
        }

        $this->newLine();
        $this->line('  <fg=yellow>Create admin user</>');

        $name = $this->ask('Full name');
        $email = $this->ask('Email');
        $password = $this->secret('Password (min. 8 characters)');

        $validator = Validator::make([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ], [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error("  ✗ {$error}");
            }

            return;
        }

        $user = $userModel::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'admin',
            'is_admin' => true,
        ]);

        $this->adminCreated = true;

        $this->newLine();
        $this->line('  <fg=green>✔ Admin user created successfully!</>');
        $this->line('     Email: <fg=cyan>' . $user->email . '</>');
        $this->line('     Role : <fg=cyan>admin</>');
        $this->line('');
        $this->line('  You can now log in at <fg=cyan>/login</>');
    }

    protected function displaySuccess(string $mode): void
    {
        $this->line('  <fg=green>✔ Installation complete!</>');
        $this->newLine();
        $this->line('  Next steps:');

        if (! $this->adminCreated) {
            $this->line('  1. Run <fg=cyan>php artisan migrate</>');
        }

        $step = $this->adminCreated ? 1 : 2;

        if (in_array($mode, ['server', 'both'])) {
            $this->line("  {$step}. Add traits to User model (see documentation)");
            $this->line('  ' . ($step + 1) . '. Visit <fg=cyan>/omni-dashboard</> to manage OAuth Clients');
        }

        if (in_array($mode, ['client', 'both'])) {
            $this->line("  {$step}. Fill in client credentials in <fg=cyan>.env</>");
            $this->line('  ' . ($step + 1) . '. Add login button: <fg=cyan>@include(\'omni::components.login-button\')</>');
        }

        $this->newLine();
        $this->line('  📖 Documentation: <fg=cyan>https://developerawam.com/omni-central-auth</>');
        $this->newLine();
    }
}
