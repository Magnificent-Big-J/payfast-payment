# v2.0.0 Release Readiness

## Current Status

Native v2 implementation foundation is in place:

- PHP 8.2-8.5 and Laravel 11-13 package constraints
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

## Stable Release Blockers

Do not tag `v2.0.0` until these are resolved:

- Remote `main` history still contains the old unwanted co-author trailer because branch protection blocks force-push cleanup.
- Disposable PayFast sandbox lifecycle has not been run.
- Sandbox response fixtures have not been captured for every subscription operation.
- Card-update link behavior on sandbox must be verified.
- PHP 8.2, 8.3, 8.4, and 8.5 matrix has not been run on GitHub Actions.
- Laravel 11, 12, and 13 matrix has not been run on GitHub Actions.
- Procurement temporary-branch installation smoke test has not been run.

## Release Decision

Use `v2.0.0` only if every stable release blocker is cleared.

Use `v2.0.0-rc.1` if the code is useful for integration testing but any sandbox, matrix, security, or compatibility gate remains unresolved.

## Authorship Gate

Before publishing, scan the working tree and commit messages for unwanted AI attribution terms. The scan must produce no package/source/release attribution findings before final publication.
