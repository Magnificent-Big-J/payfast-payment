# v2.0.0 Release Readiness

## Current Status

Native v2 implementation foundation is in place:

- PHP 8.2-8.5 and Laravel 12-13 package constraints
- Native API signing and canonicalization
- Native HTTP transport boundary and cURL transport
- Native subscription API client
- Orchestrated ITN validation client
- v1 checkout and subscription-form compatibility retained
- v2 migration and protocol docs added
- GitHub Actions workflow added for quality checks and PHP/Laravel matrix runs
- Laravel Testbench smoke coverage added for config merging and package bindings

## Local Gates Passing

Run on PHP 8.4:

```bash
vendor/bin/phpunit --colors=never
composer validate --strict --no-check-publish
composer audit
find src tests -name '*.php' -print0 | xargs -0 -n1 php -l
git diff --check
```

The PHPUnit suite includes a Laravel Testbench smoke test for package config merging and service-container bindings.

Clean Laravel install smoke test passed locally on PHP 8.4 against Laravel Framework 13.23.0:

```bash
composer create-project laravel/laravel /tmp/payfast-laravel-smoke-*
composer require rainwaves/payfast-payment:*@dev
php artisan package:discover
php artisan vendor:publish --tag=payfast-config --force
```

The temporary app resolved the legacy checkout/subscription contracts, native client, native subscription client, and ITN validator from the service container.

GitHub Actions passed on `release/v2.0.0-candidate` at commit `65920f7815ba77a7e3065451885e38d9eaf68d42`:

- Quality job
- PHP 8.2 / Laravel 12
- PHP 8.3 / Laravel 12
- PHP 8.3 / Laravel 13
- PHP 8.4 / Laravel 12
- PHP 8.4 / Laravel 13
- PHP 8.5 / Laravel 12
- PHP 8.5 / Laravel 13

Remote temporary-branch install smoke test passed with:

```bash
composer config repositories.payfast vcs https://github.com/Magnificent-Big-J/payfast-payment.git
composer require rainwaves/payfast-payment:dev-release/v2.0.0-candidate
php artisan package:discover
php artisan vendor:publish --tag=payfast-config --force
```

The remote-branch package installed at commit `65920f7`, discovered its service provider, published config, and resolved the same Laravel service-container bindings as the local clean-install smoke test.

## Stable Release Blockers

Do not tag `v2.0.0` until these are resolved:

- Remote `main` history still contains the old unwanted co-author trailer because branch protection blocks force-push cleanup.
- Disposable PayFast sandbox lifecycle has not been run.
- Sandbox response fixtures have not been captured for every subscription operation.
- Card-update link behavior on sandbox must be verified.

## Release Decision

Use `v2.0.0` only if every stable release blocker is cleared.

Use `v2.0.0-rc.1` if the code is useful for integration testing but any sandbox, matrix, security, or compatibility gate remains unresolved.

## Authorship Gate

Before publishing, scan the working tree and commit messages for unwanted AI attribution terms. The scan must produce no package/source/release attribution findings before final publication.
