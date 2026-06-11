# Contributing to Tallstarter

Thank you for considering contributing to Tallstarter! Contributions of all kinds are welcome: bug fixes, new features, documentation improvements, and tests. This guide explains how to get set up and what is expected of a pull request.

## Local Setup

Requirements: PHP 8.2+, Composer, and Node.js 22+.

1. Fork the repository on GitHub and clone your fork:

```bash
git clone https://github.com/your-username/tallstarter.git
cd tallstarter
```

2. Install dependencies and build the frontend assets:

```bash
composer install
npm install
npm run build # or npm run dev
```

3. Configure your environment and database:

```bash
cp .env.example .env
php artisan key:generate
php artisan storage:link
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

4. Optionally create the first Super Admin user:

```bash
php artisan app:create-super-admin
```

5. Start the development environment (server, queue, logs, and Vite in one command):

```bash
composer run dev
```

## Before Submitting a Pull Request

Run the full review suite and make sure it passes:

```bash
composer review
```

This command runs, in order:

-   [Laravel Pint](https://github.com/laravel/pint) - code style fixes
-   [PHPStan (Larastan)](https://github.com/larastan/larastan) - static analysis
-   [Rector](https://github.com/rectorphp/rector) (dry-run) - automated refactoring checks
-   [PestPHP](https://pestphp.com) - the test suite

Every change should be covered by tests. Write a new test or update an existing one, and run the relevant tests before pushing:

```bash
php artisan test --compact --filter=YourTestName
```

## Tests

Tests are written with [PestPHP](https://pestphp.com) and live in:

-   `tests/Feature` - feature tests (most tests belong here), organized by area, e.g. `tests/Feature/Auth`, `tests/Feature/Livewire/Admin`, `tests/Feature/Teams`
-   `tests/Unit` - unit tests

Run the whole suite with:

```bash
php artisan test
```

## Branches & Pull Requests

-   Create a feature branch off `main` with a descriptive name, e.g. `feature/team-export`, `fix/2fa-recovery-codes`, or `docs/social-login`.
-   Keep pull requests focused on a single change; smaller PRs are easier to review and merge.
-   Open the pull request against the `main` branch.
-   Use a clear, descriptive title and explain **what** changed and **why** in the description.
-   The CI workflow (`.github/workflows/tests.yml`) runs the test suite on every pull request - make sure it's green.

If you're new to open source, here's a great resource on how to [contribute to open source projects](https://github.com/firstcontributions/first-contributions?tab=readme-ov-file).

## Contributors

All contributors are listed in [Contributors.md](Contributors.md). Many thanks to everyone who has helped make this project better!
