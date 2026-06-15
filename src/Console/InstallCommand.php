<?php

namespace DeveloperAwam\OmniCentralAuth\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'omni:install
                            {--mode= : Mode instalasi: server, client, atau both}
                            {--force : Timpa file yang sudah ada}';

    protected $description = 'Install dan setup package Omni Central Auth';

    public function handle(): int
    {
        $this->displayBanner();

        $mode = $this->option('mode') ?? $this->askMode();

        $this->info("⚙️  Mode dipilih: [{$mode}]");
        $this->newLine();

        // Publish config
        $this->publishConfig();

        // Publish migrations
        $this->publishMigrations();

        // Publish views
        if ($this->confirm('Publish views untuk dikustomisasi?', false)) {
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

        $this->newLine();
        $this->displaySuccess($mode);

        return self::SUCCESS;
    }

    protected function askMode(): string
    {
        return $this->choice(
            'Apa peran aplikasi ini?',
            [
                'server' => 'server — Aplikasi ini adalah SSO Server (Identity Provider)',
                'client' => 'client — Aplikasi ini adalah Aplikasi Klien (Service Provider)',
                'both'   => 'both   — Keduanya (untuk development)',
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
        $this->line('  <fg=green>✔</> Config dipublish ke <fg=cyan>config/omni-central-auth.php</>');
    }

    protected function publishMigrations(): void
    {
        $this->callSilently('vendor:publish', [
            '--tag'   => 'omni-migrations',
            '--force' => $this->option('force'),
        ]);
        $this->line('  <fg=green>✔</> Migrations dipublish ke <fg=cyan>database/migrations/</>');
    }

    protected function publishViews(): void
    {
        $this->callSilently('vendor:publish', [
            '--tag'   => 'omni-views',
            '--force' => $this->option('force'),
        ]);
        $this->line('  <fg=green>✔</> Views dipublish ke <fg=cyan>resources/views/vendor/omni/</>');
    }

    protected function setupServer(): void
    {
        $this->newLine();
        $this->line('  <fg=yellow>SERVER MODE SETUP</>');

        // Install Passport
        $this->call('passport:install');
        $this->line('  <fg=green>✔</> Passport keys & clients dibuat');

        // Reminder
        $this->line('  <fg=yellow>!</> Tambahkan <fg=cyan>HasApiTokens</> trait ke model User kamu');
        $this->line('  <fg=yellow>!</> Tambahkan <fg=cyan>TwoFactorAuthenticatable</> trait untuk 2FA');
    }

    protected function setupClient(): void
    {
        $this->newLine();
        $this->line('  <fg=yellow>CLIENT MODE SETUP</>');

        $this->line('  <fg=yellow>!</> Isi variabel berikut di file <fg=cyan>.env</> kamu:');
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
        $this->line("  <fg=green>✔</> .env diperbarui: <fg=cyan>{$key}={$value}</>");
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

    protected function displaySuccess(string $mode): void
    {
        $this->line('  <fg=green>✔ Instalasi selesai!</>');
        $this->newLine();
        $this->line('  Langkah selanjutnya:');
        $this->line('  1. Jalankan <fg=cyan>php artisan migrate</>');

        if (in_array($mode, ['server', 'both'])) {
            $this->line('  2. Tambahkan trait ke User model (lihat dokumentasi)');
            $this->line('  3. Buka <fg=cyan>/omni-dashboard</> untuk manage OAuth Clients');
        }

        if (in_array($mode, ['client', 'both'])) {
            $this->line('  2. Isi kredensial client di <fg=cyan>.env</>');
            $this->line('  3. Tambahkan tombol login: <fg=cyan>@include(\'omni::components.login-button\')</>');
        }

        $this->newLine();
        $this->line('  📖 Dokumentasi: <fg=cyan>https://developerawam.com/omni-central-auth</>');
        $this->newLine();
    }
}
