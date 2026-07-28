# Migration From v1 To v2

## Runtime

v2 targets:

- PHP 8.2-8.5
- Laravel 11-13

Use v1.7.x for PHP 7.4-8.1 or Laravel 10 projects.

## Configuration

Existing `PAYFAST_*` variables remain supported. v2 standardizes on `environment` internally:

```php
$payfast = \rainwaves\PayfastPayment\Client\PayFastClient::make([
    'merchant_id' => env('PAYFAST_MERCHANT_ID'),
    'merchant_key' => env('PAYFAST_MERCHANT_KEY'),
    'pass_phrase' => env('PAYFAST_PASS_PHRASE'),
    'environment' => 'sandbox',
]);
```

Legacy `env` values such as `local` still map to sandbox.

## Checkout

Existing v1 checkout wrappers remain available where practical:

```php
$payFast = new \rainwaves\PayfastPayment\PayFast($config);
$html = $payFast->makePaymentWithAForm($input)->createForm();
```

The v2 factory exposes checkout through:

```php
$html = $payfast->checkout()
    ->makePaymentWithAForm($input)
    ->createForm();
```

## Subscription Management

v2 adds native subscription-management operations:

```php
use rainwaves\PayfastPayment\Request\PauseSubscriptionRequest;

$result = $payfast->subscriptions()->pause(
    $token,
    new PauseSubscriptionRequest(1)
);

if ($result->successful()) {
    // Update application-owned subscription state.
}
```

The package does not persist subscription state. The host application owns database updates and idempotency.

## ITN Validation

Existing granular ITN methods remain available. v2 also adds one-call validation:

```php
use rainwaves\PayfastPayment\Request\ExpectedPayment;
use rainwaves\PayfastPayment\Support\Money;

$result = $payfast->itn()->validate(
    payload: $_POST,
    rawBody: file_get_contents('php://input'),
    remoteIp: $_SERVER['REMOTE_ADDR'],
    expected: new ExpectedPayment('10000100', Money::zar('499.00')),
    confirmWithPayFast: false
);

if (!$result->valid()) {
    http_response_code(400);
    exit;
}
```

Browser return URLs are not payment confirmation. Confirm payment through ITN, and keep `pf_payment_id` replay protection in the host application.

## Removed From v2 Scope

v2 does not include:

- Database migrations
- Eloquent models
- Laravel routes/controllers
- Vue/Vuetify components
- Plan/pricing/invoice logic
- Automated dunning
- Webhook persistence

