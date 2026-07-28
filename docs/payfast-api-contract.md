# PayFast API Contract

Verified from PayFast developer documentation and PayFast's public Postman collection on 2026-07-28.

Sources:

- https://developers.payfast.co.za/api
- https://documenter.getpostman.com/view/10608852/TVCmSQZu

## Authentication

API requests use:

- Header `merchant-id`
- Header `version`, currently `v1`
- Header `timestamp`, ISO-8601 format
- Header `signature`

Signature input consists of submitted header values, body values, query-string values, and the merchant passphrase. Fields are sorted alphabetically by key. Empty values are excluded. `signature` and sandbox query parameter `testing` are excluded. The signature is an MD5 hash in lowercase.

## Base URLs

| Purpose | URL |
|---|---|
| Checkout sandbox | `https://sandbox.payfast.co.za/eng/process` |
| Checkout production | `https://www.payfast.co.za/eng/process` |
| ITN validation sandbox | `https://sandbox.payfast.co.za/eng/query/validate` |
| ITN validation production | `https://www.payfast.co.za/eng/query/validate` |
| API base | `https://api.payfast.co.za` |
| Sandbox API marker | `testing=true` query parameter |

## Subscription Operations

| Operation | Method | Path | Body |
|---|---|---|---|
| Fetch subscription | `GET` | `/subscriptions/{token}/fetch` | none |
| Pause subscription | `PUT` | `/subscriptions/{token}/pause` | optional `cycles`, default 1 |
| Unpause subscription | `PUT` | `/subscriptions/{token}/unpause` | none |
| Cancel subscription | `PUT` | `/subscriptions/{token}/cancel` | none |
| Update subscription | `PATCH` | `/subscriptions/{token}/update` | at least one of `cycles`, `frequency`, `run_date`, `amount` |
| Ad hoc charge | `POST` | `/subscriptions/{token}/adhoc` | `amount`, `item_name`, optional `item_description`, `itn`, `m_payment_id`, `cc_cvv`, `setup` |

`amount` for update and ad hoc API operations is sent in cents.

## Card Update Link

PayFast documents recurring card update as a buyer redirect link:

```text
https://www.payfast.co.za/eng/recurring/update/{token}?return={return}
```

For sandbox usage this package generates the same path on `sandbox.payfast.co.za`. Verify this during sandbox release testing.

## Response Shape

PayFast API responses are JSON and commonly follow:

```json
{
  "code": 200,
  "status": "success",
  "data": {}
}
```

Errors use HTTP status codes and may include:

- `400` missing/invalid fields or malformed signature
- `401` merchant/signature/origin failure
- `404` unavailable endpoint or inaccessible resource
- `429` rate limit
- `500` PayFast application or communication failure

## Open Verification Items

- Confirm card-update sandbox host behavior.
- Capture sandbox success/error fixtures for every subscription operation.
- Confirm whether API responses always include `code`, `status`, and `data` for all operations.

