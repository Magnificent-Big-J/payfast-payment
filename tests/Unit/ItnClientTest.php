<?php

namespace rainwaves\PayfastPayment\Tests\Unit;

use PHPUnit\Framework\TestCase;
use rainwaves\PayfastPayment\Client\ItnClient;
use rainwaves\PayfastPayment\Request\ExpectedPayment;
use rainwaves\PayfastPayment\Support\Money;

class ItnClientTest extends TestCase
{
    public function testValidateReportsEachOfflineCheck(): void
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
