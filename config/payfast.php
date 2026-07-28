<?php

return [
    'merchant_id'  => env('PAYFAST_MERCHANT_ID', env('MERCHANT_ID', '')),
    'merchant_key' => env('PAYFAST_MERCHANT_KEY', env('MERCHANT_KEY', '')),
    'environment'  => env('PAYFAST_ENVIRONMENT', env('PAYFAST_ENV', env('ENVIRONMENT', 'sandbox'))),
    'env'          => env('PAYFAST_ENV', env('ENVIRONMENT', 'sandbox')),
    'return_url'   => env('PAYFAST_RETURN_URL', env('RETURN_URL', '')),
    'cancel_url'   => env('PAYFAST_CANCEL_URL', env('CANCEL_URL', '')),
    'notify_url'   => env('PAYFAST_NOTIFY_URL', env('NOTIFY_URL', '')),
    'pass_phrase'  => env('PAYFAST_PASS_PHRASE', env('PASS_PHRASE', '')),
];
