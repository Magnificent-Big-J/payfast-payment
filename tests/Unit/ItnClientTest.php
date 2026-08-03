<?php

namespace rainwaves\PayfastPayment\Tests\Unit;

use PHPUnit\Framework\TestCase;
use rainwaves\PayfastPayment\Client\ItnClient;
use rainwaves\PayfastPayment\Itn\PayFastIpValidator;
use rainwaves\PayfastPayment\Request\ExpectedPayment;
use rainwaves\PayfastPayment\Support\Money;

class ItnClientTest extends TestCase
{
    protected function tearDown(): void
    {
        PayFastIpValidator::resetResolver();
    }

    public function testValidateReportsEachOfflineCheck(): void
    {
        // PayFastIpValidator resolves real hostnames via DNS -- fake it so
        // this stays a genuinely offline test (see its own class docblock
        // for why it's DNS-based at all).
        PayFastIpValidator::fakeResolver(fn (string $hostname): array => match ($hostname) {
            'sandbox.payfast.co.za' => ['196.33.227.240'],
            default                 => [],
        });

        $payload = [
            'merchant_id' => '10000100',
            'm_payment_id' => '1234',
            'pf_payment_id' => '9876',
            'amount_gross' => '100.00',
            'item_name' => 'Test Product',
            'payment_status' => 'COMPLETE',
        ];
        $payload['signature'] = $this->sign($payload, 'secret');

        $result = (new ItnClient([
            'environment' => 'sandbox',
            'pass_phrase' => 'secret',
        ]))->validate(
            payload: $payload,
            rawBody: null,
            remoteIp: '196.33.227.240',
            expected: new ExpectedPayment('10000100', Money::zar('100.00')),
            confirmWithPayFast: false
        );

        $this->assertTrue($result->valid());
        $this->assertSame([
            'source_ip' => true,
            'signature' => true,
            'amount' => true,
            'merchant_id' => true,
        ], $result->checks());
        $this->assertSame('9876', $result->paymentId());
        $this->assertSame('COMPLETE', $result->paymentStatus());
        $this->assertSame('[redacted]', $result->payload()['signature']);
        $this->assertSame([], $result->errors());
    }

    public function testValidateDistinguishesEndpointConfirmationFailure(): void
    {
        $payload = [
            'merchant_id' => '10000100',
            'm_payment_id' => '1234',
            'pf_payment_id' => '9876',
            'amount_gross' => '100.00',
            'item_name' => 'Test Product',
            'payment_status' => 'COMPLETE',
        ];
        $payload['signature'] = $this->sign($payload, 'secret');

        PayFastIpValidator::fakeResolver(fn (string $hostname): array => match ($hostname) {
            'sandbox.payfast.co.za' => ['196.33.227.240'],
            default                 => [],
        });

        $client = new ItnClient([
            'environment' => 'sandbox',
            'pass_phrase' => 'secret',
        ], static function (): bool {
            throw new \RuntimeException('Endpoint unavailable');
        });

        $result = $client->validate(
            payload: $payload,
            rawBody: null,
            remoteIp: '196.33.227.240',
            expected: new ExpectedPayment('10000100', Money::zar('100.00')),
            confirmWithPayFast: true
        );

        $this->assertFalse($result->valid());
        $this->assertFalse($result->checks()['server_confirmation']);
        $this->assertSame(\RuntimeException::class, $result->errors()['server_confirmation']['type']);
        $this->assertSame('Endpoint unavailable', $result->errors()['server_confirmation']['message']);
    }

    private function sign(array $payload, string $passPhrase): string
    {
        $payload['passphrase'] = $passPhrase;
        ksort($payload);

        $query = http_build_query($payload, '', '&', PHP_QUERY_RFC1738);
        $query = preg_replace_callback('/%[0-9a-f]{2}/', static function (array $m): string {
            return strtoupper($m[0]);
        }, $query);

        return md5($query);
    }
}
