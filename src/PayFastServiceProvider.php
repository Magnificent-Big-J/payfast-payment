<?php

namespace rainwaves\PayfastPayment;

use Carbon\Laravel\ServiceProvider;

class PayFastServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->publishes([
            __DIR__.'/../config/payfast.php' => config_path('payfast.php'),
        ], 'payfast-config');
    }
}