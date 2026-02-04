# Payfast Payment Package

This is a PHP package for integrating with the PayFast payment gateway. It provides a convenient way to handle one-time payments and recurring billing in your PHP applications, with support for both vanilla PHP and Laravel.

## Installation

You can install the package via Composer. Run the following command in your project directory:
```php
composer require rainwaves/payfast-payment
```
## Configuration

After installing the package, you need to configure it with your PayFast credentials. In Laravel, you'll need to publish the config file and set the credentials in your .env file:
- merchant_id=XXXXXXXX
- merchant_key=XXXXXXXXXX
- env=local|production
- return_url=XXXXXXXXXXXX
- cancel_url=XXXXXXXXXXXX
- notify_url=XXXXXXXXXXXX
- pass_phrase=XXXXXXXXXXXX

## Compatibility

- PHP: 7.4+ and 8.x (including 8.3 and 8.4).
- Laravel: 8.x through 12.x.

Laravel 10+ requires PHP 8.1+, and Laravel 11+ requires PHP 8.2+. Align your project PHP version accordingly.

## Usage

### Vanilla PHP

````php 
require 'vendor/autoload.php';

$config = array('merchant_id' => 10000100,
    'merchant_key'=> '46f0cd694581a',
    'env'=> 'local',
    'return_url'=> 'https://www.example.com/success',
    'cancel_url'=> 'https://www.example.com/cancel',
    'notify_url'=> 'https://www.example.com/notify',
    'pass_phrase' => 'jt7NOE43FZPn',
);
$input = array(
    'amount' => 100.00,
    'item_name' => 'Test Product',
    'name_first' => 'First Name',
    'name_last'  => 'Last Name',
    'email_address'=> 'test@test.com',
    'm_payment_id' => '1234',
    'email_confirmation' => true,
    'custom_str1' => 'example',
);

$payFast = new PayFast($config);
echo $payFast->makePaymentWithAForm($input)->createForm();
````

A form with hidden inputs will be returned.


### Laravel
Assuming you have the necessary routes and views set up, here's an example of how to use the package in Laravel:
````php
use Illuminate\Http\Request;
use rainwaves\PayfastPayment\PayFast;

class PaymentController extends Controller
{
    public function makePayment(Request $request)
    {
        $config = [
            'merchant_id' => config('payfast.merchant_id'),
            'merchant_key' => config('payfast.merchant_key'),
            'env' => config('payfast.env'),
            'return_url' => config('payfast.return_url'),
            'cancel_url' => config('payfast.cancel_url'),
            'notify_url' => config('payfast.notify_url'),
            'pass_phrase' => config('payfast.pass_phrase'),
        ];

        $input = [
            'amount' => 100.00,
            'item_name' => 'Test Product',
            'name_first' => $request->input('name_first'),
            'name_last' => $request->input('name_last'),
            'email_address' => $request->input('email'),
            'm_payment_id' => '1234',
            'email_confirmation' => true,
            'custom_str1' => 'example',
        ];

        $payFast = new PayFast($config);
        return $payFast->makePaymentWithAForm($input)->createForm();
    }
}

````

### Subscriptions

````php
use rainwaves\PayfastPayment\PayFastSubscription;
use rainwaves\PayfastPayment\Model\Frequency;

$payFast = new PayFastSubscription($config);
$input = [
    'amount' => 100.00,
    'item_name' => 'Gold Plan',
    'billing_date' => '2026-03-01',
    'recurring_amount' => 100.00,
    'frequency' => Frequency::MONTHLY,
    'cycles' => 0, // 0 = indefinite
    'subscription_notify_email' => true,
    'subscription_notify_webhook' => true,
    'subscription_notify_buyer' => true,
];

echo $payFast->createSubscriptionWithAForm($input)->createForm();
````

### Optional Fields
- `custom_int1..5`, `custom_str1..5`
- `payment_method`
- `email_confirmation`, `confirmation_address`

### ITN (Instant Transaction Notification) Validation

PayFast requires verification of ITN messages (signature, source, and data). You can validate signatures and optionally call the PayFast validation endpoint:

````php
use rainwaves\PayfastPayment\Itn\PayFastItnValidator;

$payload = $_POST; // ITN payload
$validator = new PayFastItnValidator($payload, config('payfast.pass_phrase'));

if (!$validator->validateSignature()) {
    // invalid signature
}

if (!$validator->validateAmount('100.00')) {
    // amount mismatch
}

if (!$validator->validateMerchantId(config('payfast.merchant_id'))) {
    // merchant mismatch
}

// Optional: validate with PayFast endpoint (see PayFast docs for URL)
if (!$validator->validateWithPayFastEndpoint($validateUrl)) {
    // validation failed
}
````

## Notes
- PayFast recommends enabling a passphrase on your account for secure signature validation, and recurring billing requires a passphrase to avoid signature errors.
- Ensure you verify ITN requests per PayFast’s integration requirements (signature, source IP, and amount checks).

## Testing
To run the PHPUnit test cases for the package, use the following command:

vendor/bin/phpunit

## License
This package is open-source software licensed under the MIT license.
