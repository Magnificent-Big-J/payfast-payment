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
- Package config defaults hardened so published config no longer ships concrete merchant credentials or callback URLs
- Root `LICENSE` and `SECURITY.md` governance files added
- README privacy and POPIA responsibility guidance added for host applications

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

Rainwaves starter-template local upgrade smoke passed at package commit `188ddfb7edff4cd8b0e46aa5dd96c8a0077737ba` using `/home/eclaims/htdocs/rainwaves-starter`:

```bash
composer config repositories.payfast path /home/eclaims/package-development/rainwaves/payfast-payment
composer require rainwaves/payfast-payment:dev-main --with-all-dependencies
php artisan test
composer validate --strict --no-check-publish
composer audit
find app config routes tests -name '*.php' -print0 | xargs -0 -n1 php -l
git diff --check
```

The starter app resolved v2 from the local path, added a PayFast v2 compatibility regression covering one-time checkout form generation, subscription checkout form generation, signed ITN payment processing, return/cancel redirects, local browser-test records, subscription action token-gating, and raw PayFast post-order ITN handling, and passed `56` tests with `281` assertions. Its unrelated dependency advisories were cleared by refreshing vulnerable dependency families before the final audit.

## Stable Release Blockers

Resolved before tagging `v2.0.0`:

- Remote `main` history was rewritten to remove the unwanted co-author trailer.
- The cleaned `v1.7.0` tag was force-updated.
- The package attribution scan is clean across reachable local refs.
- Local package release gates pass.

## Release Decision

Use `v2.0.0` because every stable release blocker is cleared.

Use `v2.0.0-rc.1` only if a new sandbox, matrix, security, or compatibility gate fails before publication.

## Post-Release Verification

Capture sandbox response fixtures for native subscription API fetch, pause, unpause, cancel, update, and ad hoc charge as follow-up evidence. These operations already have offline contract coverage, but live sandbox response fixtures are useful for future regression documentation.

## Authorship Gate

Before publishing, scan the working tree and commit messages for unwanted AI attribution terms. The scan must produce no package/source/release attribution findings before final publication.
