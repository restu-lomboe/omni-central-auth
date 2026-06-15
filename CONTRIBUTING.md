# Contributing Guide

Thank you for your interest in contributing to **omni-central-auth**! 🎉

---

## Environment Setup

### Requirements

- PHP 8.2+
- Composer
- Git

### Setup

```bash
# Fork and clone the repo
git clone https://github.com/your-username/omni-central-auth.git
cd omni-central-auth

# Install dependencies
composer install

# Run tests to verify everything works
composer test
```

---

## Contribution Workflow

### 1. Create a new branch

```bash
# Branch from main
git checkout -b feat/feature-name
# or
git checkout -b fix/bug-name
```

**Branch naming conventions:**
- `feat/` — new feature
- `fix/` — bug fix
- `docs/` — documentation changes
- `refactor/` — refactoring without functional changes
- `test/` — adding or improving tests

### 2. Make your changes

Ensure your changes follow the existing code standards.

### 3. Run tests & linting

```bash
# Run all tests
composer test

# Check code style
vendor/bin/pint --test

# Auto-fix code style
vendor/bin/pint
```

### 4. Update the CHANGELOG

Add an entry under `[Unreleased]` in `CHANGELOG.md` using this format:

```md
### Added
- Description of the new feature

### Fixed
- Description of the bug fix
```

### 5. Commit & Push

```bash
git add .
git commit -m "feat: add feature X"
git push origin feat/feature-name
```

**Commit message conventions** (Conventional Commits):
- `feat:` — new feature
- `fix:` — bug fix
- `docs:` — documentation
- `refactor:` — refactoring
- `test:` — tests
- `chore:` — maintenance

### 6. Open a Pull Request

Open a PR against the `main` branch and fill in the provided template.

---

## Writing Tests

Every feature or bug fix **must** include tests. This package uses **Pest**.

```bash
# Run a specific test file
vendor/bin/pest tests/Feature/Dashboard/ClientCrudTest.php

# Run with coverage
composer test-coverage
```

Test structure:
```
tests/
├── Feature/
│   ├── Dashboard/      ← Dashboard tests
│   ├── Server/         ← SSO server tests
│   ├── Client/         ← SSO client tests
│   └── AuditLogTest.php
├── Unit/               ← Unit tests
├── Fixtures/           ← Dummy models for testing
├── Pest.php
└── TestCase.php
```

---

## Code Style

This package uses **Laravel Pint** with a PSR-12 preset.

```bash
# Check for style violations
vendor/bin/pint --test

# Auto-fix
vendor/bin/pint
```

---

## Reporting Bugs

Use [GitHub Issues](https://github.com/developerawam/omni-central-auth/issues) with the **Bug Report** template.

Please include:
- Package, Laravel, and PHP version
- Steps to reproduce
- Full stack trace
- Mode in use (`server`, `client`, or `both`)

---

## Proposing Features

Open a [GitHub Issue](https://github.com/developerawam/omni-central-auth/issues) using the **Feature Request** template.

Please discuss large feature ideas before starting work to avoid wasted effort.

---

## Questions?

Open a [GitHub Discussion](https://github.com/developerawam/omni-central-auth/discussions) or reach out via [developerawam.com](https://developerawam.com).
