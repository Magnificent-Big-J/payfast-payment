<?php

namespace rainwaves\PayfastPayment;

use Illuminate\Support\ServiceProvider;
use rainwaves\PayfastPayment\Contract\PayFastInterface;
use rainwaves\PayfastPayment\Contract\PayFastSubscriptionInterface;

class PayFastServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/payfast.php', 'payfast');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/payfast.php' => config_path('payfast.php'),
        ], 'payfast-config');

        $this->app->bind(PayFastInterface::class, function () {
            return new PayFast($this->resolveConfig());
        });

        $this->app->bind(PayFastSubscriptionInterface::class, function () {
            return new PayFastSubscription($this->resolveConfig());
        });
    }

    private function resolveConfig(): array
    {
        return [
            'merchant_id'  => config('payfast.merchant_id'),
            'merchant_key' => config('payfast.merchant_key'),
            'env'          => config('payfast.env'),
            'return_url'   => config('payfast.return_url'),
            'cancel_url'   => config('payfast.cancel_url'),
            'notify_url'   => config('payfast.notify_url'),
            'pass_phrase'  => config('payfast.pass_phrase'),
        ];
    }
}
