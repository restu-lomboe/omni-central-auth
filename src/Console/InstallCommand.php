<?php

namespace DeveloperAwam\OmniCentralAuth\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;

class InstallCommand extends Command
{
    protected bool $adminCreated = false;

    protected $signature = 'omni:install
                            {--mode= : Installation mode: server or client}
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

        // Run pending migrations to prevent duplicate errors
        // when passport:install or createAdminUser runs migrate later
        $this->runMigrations();

        // Mode-specific setup
        match ($mode) {
            'server' => $this->setupServer(),
            'client' => $this->setupClient(),
        };

        // Set env
        $this->setEnvVariable('OMNI_AUTH_MODE', $mode);

        // Create admin user
        if ($mode === 'server') {
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
        // Check if any omni migration already exists
        $existing = glob(database_path('migrations/*_create_omni_audit_logs_table.php'));
        
        if (! empty($existing)) {
            $this->line('  <fg=green>✔</> Migrations already exist — skipped');

            return;
        }

        $this->callSilently('vendor:publish', [
            '--tag' => 'omni-migrations',
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

        // Generate passport keys (safe to re-run, --force to overwrite)
        $this->call('passport:keys', ['--force' => $this->option('force')]);

        // Publish passport config
        $this->callSilently('vendor:publish', ['--tag' => 'passport-config', '--force' => $this->option('force')]);

        // Check if passport migrations already exist
        $passportPublished = ! empty(glob(database_path('migrations/*_create_oauth_auth_codes_table.php')));

        if (! $passportPublished) {
            $this->callSilently('vendor:publish', ['--tag' => 'passport-migrations']);
            $this->call('migrate');
            $this->line('  <fg=green>✔</> Passport migrations created & run');
        } else {
            $this->line('  <fg=green>✔</> Passport migrations already exist — skipped');
        }

        // Create personal access client (skip if already exists)
        if ($this->confirm('Create a personal access client?', false)) {
            $this->call('passport:client', ['--personal' => true, '--name' => config('app.name') . ' Personal Access']);
        }

        // Generate SSO signing key
        $signingKey = Str::random(64);
        $this->setEnvVariable('OMNI_CENTRAL_SIGNING_KEY', $signingKey);

        $this->line('  <fg=green>✔</> Passport keys & config ready');
        $this->newLine();
        $this->line('  <fg=green>✔ SSO Signing Key generated!</>');
        $this->line('     <fg=cyan>' . $signingKey . '</>');
        $this->line('  (auto-saved to .env as OMNI_CENTRAL_SIGNING_KEY)');

        // Create default OAuth client for SSO
        if ($this->confirm('Create an OAuth client for SSO client apps?', true)) {
            $name = $this->ask('OAuth client name', config('app.name') . ' SSO Client');
            $redirect = $this->ask('Redirect URI', 'http://localhost:8000/omni/callback');

            $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient(
                name: $name,
                redirectUris: [$redirect],
                confidential: true,
            );

            $this->newLine();
            $this->line('  <fg=green>✔ OAuth client created!</>');
            $this->line('     Client ID    : <fg=cyan>' . $client->getKey() . '</>');
            $this->line('     Client Secret: <fg=cyan>' . ($client->plainSecret ?? 'hidden') . '</>');
            $this->line('');
            $this->line('  Add these to your client app\'s <fg=cyan>.env</>:');
            $this->line('       OMNI_CLIENT_ID=' . $client->getKey());
            $this->line("       OMNI_CLIENT_SECRET={$client->plainSecret}");
            $this->line('       OMNI_CLIENT_REDIRECT_URI=' . $redirect);
            $this->line('       OMNI_CENTRAL_SIGNING_KEY=' . $signingKey);
        }

    }

    protected function setupClient(): void
    {
        $this->newLine();
        $this->line('  <fg=yellow>CLIENT MODE SETUP</>');

        $this->line('  <fg=yellow>!</> Set the following variables in your <fg=cyan>.env</> file:');
        $this->line('       OMNI_CLIENT_SERVER_URL=https://your-sso-server.com');
        $this->line('       OMNI_CLIENT_ID=your-client-id');
        $this->line('       OMNI_CLIENT_SECRET=your-client-secret');
        $this->line('       OMNI_CENTRAL_SIGNING_KEY=copy-from-server');
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

    protected function runMigrations(): void
    {
        if ($this->confirm('Run pending database migrations now?', true)) {
            $this->call('migrate');
        }
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

        $this->callSilently('migrate');

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

        if ($mode === 'server') {
            $this->line("  {$step}. Add <fg=cyan>HasApiTokens</> & <fg=cyan>TwoFactorAuthenticatable</> traits to User model");
            $this->line('  ' . ($step + 1) . '. Visit <fg=cyan>/omni-dashboard</> to manage OAuth Clients');
        }

        if ($mode === 'client') {
            $this->line("  {$step}. Fill in client credentials in <fg=cyan>.env</> (including OMNI_CENTRAL_SIGNING_KEY)");
            $this->line('  ' . ($step + 1) . '. Add login button: <fg=cyan>@include(\'omni::components.login-button\')</>');
        }

        $this->newLine();
        $this->line('  📖 Documentation: <fg=cyan>https://developerawam.com/omni-central-auth</>');
        $this->newLine();
    }
}
