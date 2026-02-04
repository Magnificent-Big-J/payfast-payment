<?php

use PHPUnit\Framework\TestCase;
use rainwaves\PayfastPayment\Itn\PayFastItnValidator;

class PayFastItnValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function testValidateSignaturePasses()
    {
        $data = [
            'merchant_id' => '10000100',
            'merchant_key' => '46f0cd694581a',
            'm_payment_id' => '1234',
            'amount_gross' => '100.00',
            'item_name' => 'Test Product',
            'payment_status' => 'COMPLETE',
        ];

        $passPhrase = 'secret';
        $signature = $this->sign($data, $passPhrase);
        $data['signature'] = $signature;

        $validator = new PayFastItnValidator($data, $passPhrase);

        $this->assertTrue($validator->validateSignature());
    }

    private function sign(array $data, string $passPhrase): string
    {
        $data['passphrase'] = $passPhrase;
        ksort($data);

        $query = http_build_query($data, '', '&', PHP_QUERY_RFC1738);
        $query = preg_replace_callback('/%[0-9a-f]{2}/', function ($match) {
            return strtoupper($match[0]);
        }, $query);

        return md5($query);
    }
}
