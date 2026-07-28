<?php

namespace rainwaves\PayfastPayment\Tests\Unit;

use PHPUnit\Framework\TestCase;
use rainwaves\PayfastPayment\Security\PayloadCanonicalizer;
use rainwaves\PayfastPayment\Security\RequestSigner;
use rainwaves\PayfastPayment\Security\Redactor;
use rainwaves\PayfastPayment\Support\Money;
use rainwaves\PayfastPayment\Support\RouteResolver;

class PayFastApiSignatureTest extends TestCase
{
    public function testApiSignatureCanonicalizesHeadersBodyQueryAndPassphrase(): void
    {
        $headers = [
            'merchant-id' => '10000100',
            'version' => 'v1',
            'timestamp' => '2020-03-23T09:46:06+02:00',
            'signature' => '',
        ];
        $body = ['cycles' => 1];
        $query = ['testing' => 'true'];

        $canonical = (new PayloadCanonicalizer())->forApiSignature($headers, $body, $query, 'secret');

        $this->assertSame(
            'cycles=1&merchant-id=10000100&passphrase=secret&timestamp=2020-03-23T09%3A46%3A06%2B02%3A00&version=v1',
            $canonical
        );
        $this->assertSame(
            'd46a6a5825d2d176031de7ae030332f8',
            (new RequestSigner())->signApi($headers, $body, $query, 'secret')
        );
    }

    public function testMoneyStoresDecimalAndCentsWithoutPublicFloats(): void
    {
        $money = Money::zar('49.90');

        $this->assertSame('49.90', $money->toDecimal());
        $this->assertSame(4990, $money->toCents());
    }

    public function testRouteResolverBuildsSandboxApiAndCardUpdateUrls(): void
    {
        $routes = new RouteResolver();

        $this->assertSame(
            'https://api.payfast.co.za/subscriptions/token-123/fetch?testing=true',
            $routes->subscriptionUrl('token-123', 'fetch', 'sandbox')
        );
        $this->assertSame(
            'https://sandbox.payfast.co.za/eng/recurring/update/token-123?return=https%3A%2F%2Fexample.com%2Fbilling',
            $routes->recurringCardUpdateUrl('token-123', 'https://example.com/billing', 'sandbox')
        );
    }

    public function testRedactorHidesSecretsAndFullTokens(): void
    {
        $redacted = (new Redactor())->redact([
            'merchant_key' => 'secret-key',
            'passphrase' => 'secret-pass',
            'token' => '2afa4575-5628-051a-d0ed-4e071b56a7b0',
        ]);

        $this->assertSame('[redacted]', $redacted['merchant_key']);
        $this->assertSame('[redacted]', $redacted['passphrase']);
        $this->assertSame('2afa45...a7b0', $redacted['token']);
    }
}

