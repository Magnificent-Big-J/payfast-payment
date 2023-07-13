# Payfast Payment Package

This is a PHP package for integrating with the [PayFast](https://developers.payfast.co.za/docs#home) payment gateway. It provides a convenient way to handle one-time payments and recurring billing in your PHP applications, with support for both vanilla PHP and Laravel.

## Installation

You can install the PayGate Payment Package via Composer. Run the following command in your project directory:
```php
composer require rainwaves/payfast-payment
```
## Configuration

After installing the package, you need to configure it with your PayGate credentials. In Laravel, you'll need to publish the config file and set the credentials in your .env file:
- merchant_id=XXXXXXXX
- merchant_key=XXXXXXXXXX
- env=XXXXXXXX
- return_url=XXXXXXXXXXXX
- cancel_url=XXXXXXXXXXXX
- notify_url=XXXXXXXXXXXX
- pass_phrase=XXXXXXXXXXXX

## Usage

### Vanilla PHP

````php 
require 'vendor/autoload.php';

$config = array('merchant_id' => 10000100,
    'merchant_key'=> env('MERCHANT_KEY', '46f0cd694581a'),
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
);

$payFast = new PayFast($config);
echo $payFast->makePaymentWithAForm($input)->createForm();
````

A form with hidden input will be returned


### Laravel
Assuming you have the necessary routes and views set up, here's an example of how to use the PayGate Payment Package in Laravel:
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
        ];

        $payFast = new PayFast($config);
        return $payFast->makePaymentWithAForm($input)->createForm();
    }
}

````
## Testing
To run the PHPUnit test cases for the package, use the following command:

vendor/bin/phpunit

## License
This package is open-source software licensed under the MIT license.
