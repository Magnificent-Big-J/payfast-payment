<?php

namespace rainwaves\PayfastPayment\Tests\Unit;

use PHPUnit\Framework\TestCase;
use rainwaves\PayfastPayment\Itn\PayFastIpValidator;
use rainwaves\PayfastPayment\Itn\PayFastItnValidator;
use rainwaves\PayfastPayment\Response\PayFastResponse;
use rainwaves\PayfastPayment\Response\PayFastSubscriptionResponse;

class PayFastItnValidatorTest extends TestCase
{
    private array $baseData;
    private string $passPhrase = 'secret';

    protected function setUp(): void
    {
        $this->baseData = [
            'merchant_id'    => '10000100',
            'merchant_key'   => '46f0cd694581a',
            'm_payment_id'   => '1234',
            'pf_payment_id'  => '9876',
            'amount_gross'   => '100.00',
            'item_name'      => 'Test Product',
            'payment_status' => 'COMPLETE',
        ];
    }

    // --- Signature validation ---

    public function testValidateSignaturePasses(): void
    {
        $data              = $this->baseData;
        $data['signature'] = $this->sign($data, $this->passPhrase);

        $validator = new PayFastItnValidator($data, $this->passPhrase);

        $this->assertTrue($validator->validateSignature());
    }

    public function testValidateSignatureFailsWithWrongPassphrase(): void
    {
        $data              = $this->baseData;
        $data['signature'] = $this->sign($data, $this->passPhrase);

        $validator = new PayFastItnValidator($data, 'wrong-passphrase');

        $this->assertFalse($validator->validateSignature());
    }

    public function testValidateSignatureReturnsFalseWhenSignatureMissing(): void
    {
        $validator = new PayFastItnValidator($this->baseData, $this->passPhrase);

        $this->assertFalse($validator->validateSignature());
    }

    public function testValidateSignatureFromRawBody(): void
    {
        $data = $this->baseData;

        // Construct the raw body signature the same way the PayFast server does
        $signed = $data;
        $signed['passphrase'] = $this->passPhrase;
        ksort($signed);
        $signature = md5(http_build_query($signed, '', '&', PHP_QUERY_RFC1738));

        ksort($data);
        $rawBody = http_build_query($data, '', '&', PHP_QUERY_RFC1738) . '&signature=' . $signature;
        $data['signature'] = $signature;

        $validator = new PayFastItnValidator($data, $this->passPhrase, $rawBody);

        $this->assertTrue($validator->validateSignature());
    }

    public function testValidateSignatureFromPayFastRawPostOrder(): void
    {
        $rawBody = 'm_payment_id=1234&pf_payment_id=9876&payment_status=COMPLETE&item_name=Test+Product&amount_gross=100.00&merchant_id=10000100&merchant_key=46f0cd694581a';
        $signature = md5($rawBody . '&passphrase=' . urlencode($this->passPhrase));

        parse_str($rawBody . '&signature=' . $signature, $data);

        $validator = new PayFastItnValidator($data, $this->passPhrase, $rawBody . '&signature=' . $signature);

        $this->assertTrue($validator->validateSignature());
    }

    // --- Amount validation ---

    public function testValidateAmountPasses(): void
    {
        $validator = new PayFastItnValidator($this->baseData);

        $this->assertTrue($validator->validateAmount('100.00'));
        $this->assertTrue($validator->validateAmount('100'));
        $this->assertTrue($validator->validateAmount('100.0'));
    }

    public function testValidateAmountFailsOnMismatch(): void
    {
        $validator = new PayFastItnValidator($this->baseData);

        $this->assertFalse($validator->validateAmount('200.00'));
    }

    public function testValidateAmountReturnsFalseWhenFieldMissing(): void
    {
        $data = $this->baseData;
        unset($data['amount_gross']);

        $this->assertFalse((new PayFastItnValidator($data))->validateAmount('100.00'));
    }

    // --- Merchant ID validation ---

    public function testValidateMerchantIdPasses(): void
    {
        $this->assertTrue((new PayFastItnValidator($this->baseData))->validateMerchantId('10000100'));
    }

    public function testValidateMerchantIdFailsOnMismatch(): void
    {
        $this->assertFalse((new PayFastItnValidator($this->baseData))->validateMerchantId('99999999'));
    }

    // --- Payment ID (replay attack protection) ---

    public function testGetPaymentIdReturnsId(): void
    {
        $this->assertSame('9876', (new PayFastItnValidator($this->baseData))->getPaymentId());
    }

    public function testGetPaymentIdReturnsNullWhenAbsent(): void
    {
        $data = $this->baseData;
        unset($data['pf_payment_id']);

        $this->assertNull((new PayFastItnValidator($data))->getPaymentId());
    }

    // --- Payment status ---

    public function testGetPaymentStatus(): void
    {
        $this->assertSame('COMPLETE', (new PayFastItnValidator($this->baseData))->getPaymentStatus());
    }

    // --- Response hydration ---

    public function testResponseReturnsPayFastResponse(): void
    {
        $data              = $this->baseData;
        $data['signature'] = $this->sign($data, $this->passPhrase);

        $response = (new PayFastItnValidator($data, $this->passPhrase))->response();

        $this->assertInstanceOf(PayFastResponse::class, $response);
        $this->assertTrue($response->isComplete());
    }

    public function testResponseReturnsSubscriptionResponseWhenTokenPresent(): void
    {
        $data = array_merge($this->baseData, [
            'token'        => 'sub-token-abc',
            'billing_date' => '2026-05-01',
        ]);
        $data['signature'] = $this->sign($data, $this->passPhrase);

        $response = (new PayFastItnValidator($data, $this->passPhrase))->response();

        $this->assertInstanceOf(PayFastSubscriptionResponse::class, $response);
        $this->assertSame('sub-token-abc', $response->token);
    }

    public function testResponseHandlesMissingOptionalFields(): void
    {
        $response = (new PayFastItnValidator($this->baseData))->response();

        $this->assertNull($response->customStr1);
        $this->assertNull($response->amountFee);
        $this->assertNull($response->nameLast);
    }

    // --- IP allowlist validation ---

    public function testIpValidatorAcceptsKnownProductionIp(): void
    {
        // 197.97.145.144/28 covers .144 – .159
        $this->assertTrue(PayFastIpValidator::isValid('197.97.145.150', false));
        // 41.74.179.192/27 covers .192 – .223
        $this->assertTrue(PayFastIpValidator::isValid('41.74.179.200', false));
    }

    public function testIpValidatorRejectsUnknownIp(): void
    {
        $this->assertFalse(PayFastIpValidator::isValid('1.2.3.4', false));
        $this->assertFalse(PayFastIpValidator::isValid('197.97.145.160', false)); // just outside /28
    }

    public function testIpValidatorAcceptsSandboxIp(): void
    {
        // 196.33.227.224/27 covers .224 – .255
        $this->assertTrue(PayFastIpValidator::isValid('196.33.227.240', true));
    }

    public function testIpValidatorRejectsProductionIpInSandboxMode(): void
    {
        $this->assertFalse(PayFastIpValidator::isValid('197.97.145.150', true));
    }

    public function testValidateSourceIpMethodDelegatesToIpValidator(): void
    {
        $validator = new PayFastItnValidator($this->baseData);

        $this->assertFalse($validator->validateSourceIp('10.0.0.1', false));
        $this->assertTrue($validator->validateSourceIp('41.74.179.200', false));
    }

    // --- Helpers ---

    private function sign(array $data, string $passPhrase): string
    {
        $data['passphrase'] = $passPhrase;
        ksort($data);

        $query = http_build_query($data, '', '&', PHP_QUERY_RFC1738);
        $query = preg_replace_callback('/%[0-9a-f]{2}/', static function (array $m): string {
            return strtoupper($m[0]);
        }, $query);

        return md5($query);
    }
}
