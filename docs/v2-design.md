# v2 Design Proposal (Draft)

This document captures the proposed v2 scope for the Payfast package. It is intentionally comprehensive and intended for future review before implementation.

## Goals
- Provide a complete, optional persistence layer (migrations).
- Provide optional Vue 3 + Vuetify 3 UI components.
- Provide optional Laravel controllers/routes for ITN, checkout, and admin views.
- Preserve the ability to use the package in a minimal, headless way.

## Non-Goals
- Building a full admin application.
- Replacing project-specific business logic.

## Versioning Strategy
- v2.0.0 introduces migrations, routes/controllers, and Vue components.
- v1.x remains the minimal API-only version.

## Database Schema (Full Audit)

### `payfast_transactions`
- `id` (bigint, pk)
- `uuid` (uuid, unique)
- `m_payment_id` (string, index)
- `pf_payment_id` (string, index)
- `merchant_id` (string, index)
- `amount_gross` (decimal 10,2)
- `amount_fee` (decimal 10,2)
- `amount_net` (decimal 10,2)
- `currency` (string, nullable)
- `payment_status` (string, index)
- `payment_method` (string, nullable)
- `item_name` (string)
- `item_description` (text, nullable)
- `name_first` (string, nullable)
- `name_last` (string, nullable)
- `email_address` (string, nullable)
- `cell_number` (string, nullable)
- `custom_int1..5` (string, nullable)
- `custom_str1..5` (string, nullable)
- `signature` (string, nullable)
- `raw_payload` (json)
- `paid_at` (timestamp, nullable)
- `created_at`, `updated_at`

Indexes:
- `m_payment_id`, `pf_payment_id`, `payment_status`, `created_at`

### `payfast_subscriptions`
- `id` (bigint, pk)
- `uuid` (uuid, unique)
- `m_payment_id` (string, index)
- `token` (string, index)
- `merchant_id` (string, index)
- `billing_date` (date)
- `recurring_amount` (decimal 10,2)
- `frequency` (int)
- `cycles` (int)
- `subscription_notify_email` (bool)
- `subscription_notify_webhook` (bool)
- `subscription_notify_buyer` (bool)
- `status` (string, index)
- `last_billed_at` (timestamp, nullable)
- `next_billing_at` (timestamp, nullable)
- `custom_int1..5` (string, nullable)
- `custom_str1..5` (string, nullable)
- `signature` (string, nullable)
- `raw_payload` (json)
- `created_at`, `updated_at`

Indexes:
- `token`, `m_payment_id`, `status`

### `payfast_itn_logs`
- `id` (bigint, pk)
- `event_type` (string)
- `pf_payment_id` (string, index, nullable)
- `token` (string, index, nullable)
- `merchant_id` (string, index)
- `payload` (json)
- `signature_valid` (bool)
- `amount_valid` (bool)
- `merchant_valid` (bool)
- `source_ip` (string, nullable)
- `user_agent` (string, nullable)
- `received_at` (timestamp)
- `created_at`

Indexes:
- `pf_payment_id`, `token`, `received_at`

## Laravel Controllers + Routes

### Public/Checkout
- `POST /payfast/checkout`
  - Returns form payload to submit to PayFast.
- `POST /payfast/subscription`
  - Returns subscription form payload.

### ITN
- `POST /payfast/itn`
  - Validates signature, amount, merchant.
  - Optionally calls PayFast validation endpoint.
  - Persists ITN log.

### Admin (Guarded)
- `GET /payfast/transactions`
- `GET /payfast/subscriptions`
- `GET /payfast/itn-logs`

Guarding:
- Configurable `admin_guard` and `admin_middleware`.

## Vue 3 + Vuetify 3 Components (Publishable)
- `PayFastCheckoutForm.vue`
- `PayFastSubscriptionForm.vue`
- `PayFastStatusBadge.vue`
- `PayFastTransactionTable.vue`
- `PayFastSubscriptionTable.vue`
- `PayFastItnLogTable.vue`

## Config Additions (Proposed)
- `enable_routes` (bool)
- `enable_migrations` (bool)
- `enable_vue_publish` (bool)
- `admin_guard` (string)
- `admin_middleware` (array)
- `itn_validate_endpoint` (string)
- `validate_ip` (bool)
- `require_passphrase` (bool)

## Package Layout (Proposed)
- `database/migrations/`
- `resources/js/components/`
- `routes/payfast.php`
- `src/Http/Controllers/`
- `src/Models/` (optional Eloquent models)
- `config/payfast.php` (expanded)

## Open Questions
- Should models be included or leave persistence to host app?
- Provide a policy class or rely on middleware?
- Should ITN controller enqueue validation job for reliability?

