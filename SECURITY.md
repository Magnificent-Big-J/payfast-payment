# Security Policy

## Supported Versions

Security fixes are provided for the latest stable major release.

| Version | Supported |
|---|---|
| 2.x | Yes |
| 1.x | Critical fixes only |

## Reporting A Vulnerability

Report suspected vulnerabilities privately by emailing the package maintainer listed in `composer.json`.

Please include:

- Affected package version or commit
- Reproduction steps
- Expected and actual behavior
- Any relevant PayFast environment details, without sharing live secrets

Do not include production merchant keys, passphrases, full subscription tokens, customer personal information, or raw ITN payloads unless they have been redacted.

## Handling

Reports will be reviewed before public disclosure. If a fix is required, the patched release notes will describe impact and upgrade guidance without exposing exploit details unnecessarily.
