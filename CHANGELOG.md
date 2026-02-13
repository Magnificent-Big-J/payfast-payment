# Changelog

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
