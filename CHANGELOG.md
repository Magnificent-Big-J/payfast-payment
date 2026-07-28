# Changelog

## v2.0.0 - Unreleased

### Added
- Native v2 client factory via `Client\PayFastClient::make()`.
- Native subscription API client for fetch, pause, unpause, cancel, update, ad hoc charge, and card-update link generation.
- API signing, canonicalization, route resolution, HTTP request/response objects, cURL transport, response decoder, clock, redactor, and typed result objects.
- Orchestrated ITN validation client with per-check result reporting.
- v2 API contract and migration documentation.
- Offline tests for API signing, money handling, redaction, route generation, and subscription request construction.

### Changed
- v2 runtime target is PHP 8.2-8.5 and Laravel 11-13.
- `environment` is the preferred config key; legacy `env` values such as `local` still map to sandbox.
- `docs/v2-design.md` now points to the native/headless v2 direction and no longer proposes migrations, routes, controllers, or Vue components.

### Security
- API calls go through a transport boundary with TLS verification, timeouts, redirects disabled, bounded response handling, and no automatic retry for mutation calls.
- Sensitive fields and token-like values are redacted from diagnostic payloads.

## v1.7.0 - 2026-04-24

### Added
- Laravel 13 support (`orchestra/testbench ^11.0`).
- PHP 8.5 support added to `composer.json`.
- `illuminate/support ^10|^11|^12|^13` declared as an explicit `require` dependency.
- `getRequest()` added to `PayFastInterface` and `PayFastSubscriptionInterface` — the method was already implemented on both clients but absent from the contracts, breaking static analysis.
- `PayFastException::notInitialized()` named constructor for clearer error when `createForm()` is called before the payment/subscription builder method.

### Changed
- Config env keys now use a `PAYFAST_` prefix (`PAYFAST_MERCHANT_ID`, `PAYFAST_MERCHANT_KEY`, `PAYFAST_ENV`, `PAYFAST_RETURN_URL`, `PAYFAST_CANCEL_URL`, `PAYFAST_NOTIFY_URL`, `PAYFAST_PASS_PHRASE`). The old unprefixed names remain as fallbacks for backwards compatibility.
- `PayFastServiceProvider`: config is now read inside the IoC closure (lazy), rather than captured at `boot()` time. This respects deferred config resolution and dynamic config changes.
- Amount validation now enforces the PayFast maximum of `999,999.99` (`MAX_AMOUNT` was already defined but never applied to the validator).
- `recurring_amount` in subscription validation also enforces `MAX_AMOUNT`.
- `createForm()` on both clients now throws `PayFastException` with a clear message if called before the builder method, instead of triggering a PHP `TypeError` on uninitialized property access.

### Fixed
- `PayFastItnValidator::validateSignatureFromRawBody()`: passphrase was appended at the end of the query string instead of being inserted at its correct alphabetical position. PayFast sorts all fields (including passphrase) before signing, so the verification hash never matched. Fixed by parsing the raw body, inserting passphrase, sorting, and re-encoding consistently with `PayFastSignatureHelper::buildQuery`.
- Test: `testResponseHandlesMissingOptionalFields` incorrectly asserted `mPaymentId` was null while `m_payment_id` was present in the base fixture. Assertion updated to check `nameLast`, which is genuinely absent.

---

## v1.6.0 - 2026-02-13
- Validate ITN signatures using raw request body to preserve field order.
- Allow ITN validator to accept raw body input for signature checks.
- Document raw-body ITN validation usage.

## v1.5.0 - 2026-02-04
- Fix amount formatting and confirmation address mapping.
- Add support for `custom_int1..5` and `custom_str1..5`.
- Harden signature generation and form output escaping.
- Add ITN validation helper.
- Improve subscription validation and tests.
- Update Laravel service provider registration and config handling.
