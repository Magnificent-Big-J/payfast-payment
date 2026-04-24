<?php

namespace rainwaves\PayfastPayment\Tests\Unit;

use PHPUnit\Framework\TestCase;
use rainwaves\PayfastPayment\Exception\PayFastException;
use rainwaves\PayfastPayment\Model\Route;

class RouteTest extends TestCase
{
    public function testLocalUrlReturnsSandbox(): void
    {
        $this->assertSame(
            'https://sandbox.payfast.co.za/eng/process',
            Route::getUrl('local')
        );
    }

    public function testProductionUrlReturnsLive(): void
    {
        $this->assertSame(
            'https://www.payfast.co.za/eng/process',
            Route::getUrl('production')
        );
    }

    public function testUnknownEnvironmentThrowsPayFastException(): void
    {
        $this->expectException(PayFastException::class);
        $this->expectExceptionMessage('Unknown PayFast environment "staging"');

        Route::getUrl('staging');
    }

    public function testValidationUrlSwitchesByEnvironment(): void
    {
        $this->assertStringContainsString('sandbox', Route::getValidationUrl('local'));
        $this->assertStringContainsString('www.payfast.co.za', Route::getValidationUrl('production'));
    }
}
