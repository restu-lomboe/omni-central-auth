# Changelog

All notable changes to this package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Passkeys / WebAuthn support (v1.1 target)

---

## [1.0.0] - 2024-01-01

### Added

#### Core
- `OmniCentralAuthServiceProvider` with `php artisan about` integration
- Three operation modes: `server`, `client`, `both` via `OMNI_AUTH_MODE` config
- `php artisan omni:install` — interactive setup command with mode selection
- `optimizes()` integration for `php artisan optimize`

#### SSO Server (Identity Provider)
- Login, register, forgot password, reset password via **Laravel Fortify**
- Two-Factor Authentication (TOTP) with toggle to recovery code
- OAuth2 Authorization Code flow via **Laravel Passport**
- Custom OAuth consent page (`/omni/authorize`)
- Views: login, register, forgot-password, reset-password, two-factor-challenge, authorize

#### SSO Client (Service Provider)
- Custom Socialite driver `omni` pointing to the SSO Server
- `LoginController` — redirect to SSO Server + global logout
- `CallbackController` — auto-create/update local user from SSO data
- Blade component `<x-omni::login-button />` / `@include('omni::components.login-button')`

#### Admin Dashboard (Livewire 4 + Tailwind CSS)
- Overview with stats: total clients, total users, logins today, failed logins today
- **OAuth Clients**: full CRUD, secret blur-reveal, danger zone
- **Users & Roles**: live search, role filter, inline role update, user deletion
- **Audit Log**: event filter, user search, date range filter, color-coded badges
- Sidebar navigation with active state detection

#### Database & Models
- Migration `omni_audit_logs` with `user_id+event` and `occurred_at` indexes
- Migration `add_omni_columns_to_users_table` (for client mode)
- Migration `add_role_columns_to_users_table` (for server mode)
- `AuditLog` model with `record()` static helper and `pruneOldLogs()`

#### Middleware & Security
- `OmniAdminMiddleware` with flexible detection: `isOmniAdmin()`, `is_admin`, `role` column
- Dashboard route protection via configurable middleware stack

#### Internationalization
- Language files: `en/auth.php` and `id/auth.php` (Indonesian)
- All UI strings overridable via `php artisan vendor:publish --tag=omni-lang`

#### Developer Experience
- All assets publishable: config, views, migrations, lang (`--tag=omni-all`)
- `phpunit.xml` with SQLite in-memory setup
- Pest test suite: 6 Feature test files + 1 Unit test file (30+ test cases)

#### CI/CD
- GitHub Actions: matrix test PHP 8.2/8.3 × Laravel 11/12
- Coverage report with 80% minimum threshold
- Laravel Pint linting
- Auto-release workflow on `v*.*.*` tag push
- PR template, bug report template, feature request template

---

## Entry Format

Each version uses the following categories:

- **Added** — new features
- **Changed** — changes to existing functionality
- **Deprecated** — features that will be removed in a future version
- **Removed** — removed features
- **Fixed** — bug fixes
- **Security** — security vulnerability fixes

[Unreleased]: https://github.com/developerawam/omni-central-auth/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/developerawam/omni-central-auth/releases/tag/v1.0.0
