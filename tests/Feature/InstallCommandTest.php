<?php

use DeveloperAwam\OmniCentralAuth\Tests\TestCase;
use Illuminate\Support\Facades\File;

uses(TestCase::class);

beforeEach(function () {
    $this->envPath = base_path('.env');

    if (! file_exists($this->envPath)) {
        file_put_contents($this->envPath, "APP_KEY=base64:test\nAPP_NAME=TestApp\n");
    }

    // Create dummy migration files so the InstallCommand's glob checks skip them
    $migrationsDir = database_path('migrations');

    if (! is_dir($migrationsDir)) {
        mkdir($migrationsDir, 0755, true);
    }

    $dummy = <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void {}

    public function down(): void {}
};
PHP;

    foreach (['2024_01_01_000001_create_omni_audit_logs_table.php', '2024_01_01_000001_create_oauth_auth_codes_table.php'] as $file) {
        $path = $migrationsDir.'/'.$file;
        if (! file_exists($path)) {
            file_put_contents($path, $dummy);
        }
    }
});

afterEach(function () {
    if (file_exists($this->envPath)) {
        File::delete($this->envPath);
    }

    $migrationsDir = database_path('migrations');

    if (is_dir($migrationsDir)) {
        collect(File::files($migrationsDir))
            ->each(fn ($f) => File::delete($f->getPathname()));
    }
});

it('installs in server mode', function () {
    $this->artisan('omni:install', ['--mode' => 'server'])
        ->expectsQuestion('Publish views for customization?', false)
        ->expectsQuestion('Run pending database migrations now?', false)
        ->expectsQuestion('Create a personal access client?', false)
        ->expectsQuestion('Create an OAuth client for SSO client apps?', false)
        ->expectsQuestion('Create an admin user now? (to log in immediately)', false)
        ->assertSuccessful();

    expect(file_get_contents($this->envPath))->toContain('OMNI_AUTH_MODE=server');
});

it('installs in client mode', function () {
    $this->artisan('omni:install', ['--mode' => 'client'])
        ->expectsQuestion('Publish views for customization?', false)
        ->expectsQuestion('Run pending database migrations now?', false)
        ->assertSuccessful();

    expect(file_get_contents($this->envPath))->toContain('OMNI_AUTH_MODE=client');
});

it('sets OMNI_AUTH_MODE in env file', function () {
    $content = file_get_contents($this->envPath);
    $content = preg_replace('/^OMNI_AUTH_MODE=.*/m', '', $content);
    file_put_contents($this->envPath, $content);

    $this->artisan('omni:install', ['--mode' => 'client'])
        ->expectsQuestion('Publish views for customization?', false)
        ->expectsQuestion('Run pending database migrations now?', false)
        ->assertSuccessful();

    $envContent = file_get_contents($this->envPath);
    expect($envContent)->toContain('OMNI_AUTH_MODE=client');
});

it('sets OMNI_CENTRAL_SIGNING_KEY in env during server install', function () {
    $content = file_get_contents($this->envPath);
    $content = preg_replace('/^OMNI_CENTRAL_SIGNING_KEY=.*/m', '', $content);
    file_put_contents($this->envPath, $content);

    $this->artisan('omni:install', ['--mode' => 'server'])
        ->expectsQuestion('Publish views for customization?', false)
        ->expectsQuestion('Run pending database migrations now?', false)
        ->expectsQuestion('Create a personal access client?', false)
        ->expectsQuestion('Create an OAuth client for SSO client apps?', false)
        ->expectsQuestion('Create an admin user now? (to log in immediately)', false)
        ->assertSuccessful();

    $envContent = file_get_contents($this->envPath);
    expect($envContent)->toContain('OMNI_CENTRAL_SIGNING_KEY=');
});

it('updates existing env variable instead of duplicating', function () {
    file_put_contents($this->envPath, "OMNI_AUTH_MODE=client\nAPP_NAME=Test\n");

    $this->artisan('omni:install', ['--mode' => 'server'])
        ->expectsQuestion('Publish views for customization?', false)
        ->expectsQuestion('Run pending database migrations now?', false)
        ->expectsQuestion('Create a personal access client?', false)
        ->expectsQuestion('Create an OAuth client for SSO client apps?', false)
        ->expectsQuestion('Create an admin user now? (to log in immediately)', false)
        ->assertSuccessful();

    $envContent = file_get_contents($this->envPath);
    expect(substr_count($envContent, 'OMNI_AUTH_MODE'))->toBe(1);
    expect($envContent)->toContain('OMNI_AUTH_MODE=server');
});

it('does nothing if env file does not exist', function () {
    File::delete($this->envPath);

    $this->artisan('omni:install', ['--mode' => 'client'])
        ->expectsQuestion('Publish views for customization?', false)
        ->expectsQuestion('Run pending database migrations now?', false)
        ->assertSuccessful();
});

it('skips admin creation when declined', function () {
    $userModel = config('omni-central-auth.user_model');

    $this->artisan('omni:install', ['--mode' => 'server'])
        ->expectsQuestion('Publish views for customization?', false)
        ->expectsQuestion('Run pending database migrations now?', false)
        ->expectsQuestion('Create a personal access client?', false)
        ->expectsQuestion('Create an OAuth client for SSO client apps?', false)
        ->expectsQuestion('Create an admin user now? (to log in immediately)', false)
        ->assertSuccessful();

    expect($userModel::count())->toBe(0);
});

it('creates an OAuth client when confirmed', function () {
    $this->artisan('omni:install', ['--mode' => 'server'])
        ->expectsQuestion('Publish views for customization?', false)
        ->expectsQuestion('Run pending database migrations now?', false)
        ->expectsQuestion('Create a personal access client?', false)
        ->expectsQuestion('Create an OAuth client for SSO client apps?', true)
        ->expectsQuestion('OAuth client name', 'My App')
        ->expectsQuestion('Redirect URI', 'https://myapp.test/omni/callback')
        ->expectsQuestion('Create an admin user now? (to log in immediately)', false)
        ->assertSuccessful();
});

it('outputs client setup instructions', function () {
    $this->artisan('omni:install', ['--mode' => 'client'])
        ->expectsQuestion('Publish views for customization?', false)
        ->expectsQuestion('Run pending database migrations now?', false)
        ->assertSuccessful()
        ->expectsOutputToContain('OMNI_CLIENT_SERVER_URL');
});
