# Omni Central Auth

**A plug-and-play SSO solution for Laravel — be the Identity Provider or connect as a client.**

[![Latest Version](https://img.shields.io/packagist/v/developerawam/omni-central-auth.svg)](https://packagist.org/packages/developerawam/omni-central-auth)
[![Tests](https://github.com/developerawam/omni-central-auth/actions/workflows/tests.yml/badge.svg)](https://github.com/developerawam/omni-central-auth/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/developerawam/omni-central-auth.svg)](https://packagist.org/packages/developerawam/omni-central-auth)
[![License](https://img.shields.io/packagist/l/developerawam/omni-central-auth.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-11%2B-red)](https://laravel.com)

---

## About

`omni-central-auth` is a Laravel package that lets you build your own **Single Sign-On (SSO)** system — either as:

- **Identity Provider (SSO Server)** — one central login for all your applications
- **Service Provider (Client App)** — an application that delegates authentication to the SSO Server

Built on top of **Laravel Passport** (OAuth2), **Laravel Fortify** (Auth UI), and **Laravel Socialite** (OAuth2 Client).

---

## Requirements

- PHP 8.2+
- Laravel 11+
- ext-sodium (required by Laravel Passport for JWT signing)

> **Windows / XAMPP users:** Enable sodium in `php.ini` by uncommenting `;extension=sodium` → `extension=sodium`, then restart Apache.

---

## Installation

```bash
composer require developerawam/omni-central-auth
```

Run the interactive install command:

```bash
php artisan omni:install
```

The installer will guide you through the following steps:

1. **Choose a mode** — `server`, `client`, or `both`
2. **Publish config** — `config/omni-central-auth.php`
3. **Publish migrations** — copied to `database/migrations/`
4. **Run migrations** — creates all required tables
5. **Passport setup** _(server mode only)_ — generates encryption keys and creates:
    - Personal Access Client
    - Password Grant Client
6. **`.env` updated** — `OMNI_AUTH_MODE` is set automatically

---

## Mode: `server` (Identity Provider)

### 1. Add traits to your User model

```php
use Laravel\Passport\HasApiTokens;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasApiTokens, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',      // required by omni-central-auth
        'is_admin',  // required by omni-central-auth
    ];
}
```

### 2. Set mode in `.env`

```env
OMNI_AUTH_MODE=server
```

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Register

Open `/register` and create your first account. The **first registered user is automatically set as admin**.

### 5. Open the Admin Dashboard

```
http://your-app.com/omni-dashboard
```

### 6. Create an OAuth Client for each client app

Go to `/omni-dashboard/clients/create` and fill in:

- **App Name** — e.g. `HR Application`
- **Redirect URI** — the callback URL on the client app, e.g. `http://client-app.com/omni/callback`

> Do **not** use Client ID `1` or `2` — those are created automatically by Passport for internal use and do not support Authorization Code flow.

After creating, copy the **Client ID** and **Client Secret** — you will need them in the client app.

---

## Mode: `client` (Service Provider)

### 1. Install the package

```bash
composer require developerawam/omni-central-auth
php artisan omni:install
# Choose: client
```

### 2. Publish and run migrations

```bash
php artisan vendor:publish --tag=omni-migrations
php artisan migrate
```

### 3. Disable Fortify views

Since the client app does not handle login UI itself, publish the Fortify config and disable its views:

```bash
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
```

In `config/fortify.php`:

```php
'views' => false,
```

### 4. Register the Socialite OAuth driver

Add to `config/services.php`:

```php
'omni' => [
    'client_id'     => env('OMNI_CLIENT_ID'),
    'client_secret' => env('OMNI_CLIENT_SECRET'),
    'redirect'      => env('OMNI_CLIENT_REDIRECT_URI'),
],
```

### 5. Set credentials in `.env`

```env
OMNI_AUTH_MODE=client
OMNI_CLIENT_SERVER_URL=http://your-sso-server.com
OMNI_CLIENT_ID=3
OMNI_CLIENT_SECRET=your-client-secret
OMNI_CLIENT_REDIRECT_URI=http://your-client-app.com/omni/callback
```

> `OMNI_CLIENT_REDIRECT_URI` must be a **full URL** and must match exactly what is registered in the SSO server dashboard.

### 6. Add the login button to your view

```blade
@include('omni::components.login-button')
```

Or manually:

```blade
<a href="{{ route('omni.login') }}">Login with Central Account</a>
```

### 7. Customize redirect after login

In `.env`:

```env
OMNI_CLIENT_HOME=/dashboard
```

---

## SSO Flow

```
[Client App]
     │
     │  User clicks "Login with Central Account"
     ▼
GET /omni/login
     │
     │  Redirect to SSO Server
     ▼
[SSO Server] /oauth/authorize
     │
     │  User logs in (if not already)
     │  User sees consent page → clicks Authorize
     ▼
[SSO Server] issues authorization code
     │
     │  Redirect back to client app
     ▼
[Client App] /omni/callback
     │
     │  Exchange code for access token
     │  Fetch user data from SSO Server /api/user
     │  Auto-create or update local user
     │  Log user in
     ▼
Redirect to OMNI_CLIENT_HOME
```

---

## Admin Dashboard

Available at `/omni-dashboard` (configurable via `config/omni-central-auth.php`).

| Feature           | Description                                                    |
| ----------------- | -------------------------------------------------------------- |
| **OAuth Clients** | Register and manage applications allowed to connect to the SSO |
| **Users & Roles** | Manage users and their access roles                            |
| **Audit Log**     | Monitor login, logout, and token activity                      |

> Only users with `is_admin = true` or `role = admin` can access the dashboard. The first registered user on a server app is automatically granted admin access.

---

## Publishing for Customization

```bash
# Config only
php artisan vendor:publish --tag=omni-config

# Views only
php artisan vendor:publish --tag=omni-views

# Migrations only
php artisan vendor:publish --tag=omni-migrations

# Language files only
php artisan vendor:publish --tag=omni-lang

# Everything at once
php artisan vendor:publish --tag=omni-all
```

---

## Full Configuration

See [`config/omni-central-auth.php`](config/omni-central-auth.php) for all available options.

---

## Roadmap

- [x] v1.0 — SSO Server + Client + Admin Dashboard
- [ ] v1.1 — Passkeys / WebAuthn support
- [ ] v1.2 — Multi-tenancy / Organization
- [ ] v2.0 — SAML 2.0 support

---

## License

MIT License. See [LICENSE](LICENSE.md) for details.

---

Built with ❤️ by [Developer Awam](https://developerawam.com)
