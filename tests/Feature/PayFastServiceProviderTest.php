<?php

namespace rainwaves\PayfastPayment\Tests\Feature;

use Orchestra\Testbench\TestCase;
use rainwaves\PayfastPayment\Client\ItnClient;
use rainwaves\PayfastPayment\Client\PayFastClient as NativePayFastClient;
use rainwaves\PayfastPayment\Client\SubscriptionClient;
use rainwaves\PayfastPayment\Contract\ItnValidatorInterface;
use rainwaves\PayfastPayment\Contract\PayFastInterface;
use rainwaves\PayfastPayment\Contract\PayFastSubscriptionInterface;
use rainwaves\PayfastPayment\Contract\SubscriptionClientInterface;
use rainwaves\PayfastPayment\PayFast;
use rainwaves\PayfastPayment\PayFastServiceProvider;
use rainwaves\PayfastPayment\PayFastSubscription;

class PayFastServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [PayFastServiceProvider::class];
    }

    public function testDefaultPackageConfigIsMerged(): void
    {
        $this->assertSame('', config('payfast.merchant_id'));
        $this->assertSame('sandbox', config('payfast.environment'));
        $this->assertSame('', config('payfast.return_url'));
    }

    public function testLaravelContainerResolvesLegacyAndNativeBindings(): void
    {
        $this->configurePayFast();

        $this->assertInstanceOf(PayFast::class, $this->app->make(PayFastInterface::class));
        $this->assertInstanceOf(PayFastSubscription::class, $this->app->make(PayFastSubscriptionInterface::class));
        $this->assertInstanceOf(NativePayFastClient::class, $this->app->make(NativePayFastClient::class));
        $this->assertInstanceOf(SubscriptionClient::class, $this->app->make(SubscriptionClientInterface::class));
        $this->assertInstanceOf(ItnClient::class, $this->app->make(ItnValidatorInterface::class));
    }

    private function configurePayFast(): void
    {
        config()->set('payfast.merchant_id', '10000100');
        config()->set('payfast.merchant_key', '46f0cd694581a');
        config()->set('payfast.environment', 'sandbox');
        config()->set('payfast.env', 'sandbox');
        config()->set('payfast.return_url', 'https://example.com/success');
        config()->set('payfast.cancel_url', 'https://example.com/cancel');
        config()->set('payfast.notify_url', 'https://example.com/notify');
        config()->set('payfast.pass_phrase', 'secret');
    }
}
