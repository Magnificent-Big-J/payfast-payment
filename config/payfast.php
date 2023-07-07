
<?php

return [
  'merchant_id'=> env('MERCHANT_ID', '10000100'),
  'merchant_key'=> env('MERCHANT_KEY', '46f0cd694581a'),
  'env'=> env('ENVIRONMENT', 'local'),
  'return_url'=> env('RETURN_URL', 'https://my.application.com/return'),
  'cancel_url'=>env('CANCEL_URL', 'https://my.application.com/cancel'),
  'notify_url'=>''
];