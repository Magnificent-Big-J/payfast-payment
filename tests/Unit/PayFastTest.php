<?php

namespace rainwaves\PayfastPayment\Tests\Unit;

use Orchestra\Testbench\TestCase;
use rainwaves\PayfastPayment\PayFast;
use rainwaves\PayfastPayment\Request\PayFastRequest;

class PayFastTest extends TestCase
{
    protected array $config;
    protected array $input;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'merchant_id'  => 10000100,
            'merchant_key' => '46f0cd694581a',
            'env'          => 'local',
            'return_url'   => 'https://www.example.com/success',
            'cancel_url'   => 'https://www.example.com/cancel',
            'notify_url'   => 'https://www.example.com/notify',
            'pass_phrase'  => 'jt7NOE43FZPn',
        ];

        $this->input = [
            'amount'             => 100.00,
            'item_name'          => 'Test Product',
            'name_first'         => 'First Name',
            'name_last'          => 'Last Name',
            'email_address'      => 'test@test.com',
            'm_payment_id'       => '1234',
            'email_confirmation' => true,
        ];
    }

    public function testCreateFormContainsExpectedFields(): void
    {
        $client = new PayFast($this->config);
        $client->makePaymentWithAForm($this->input);
        $form = $client->createForm();

        $this->assertStringContainsString('amount', $form);
        $this->assertStringContainsString('Test Product', $form);
        $this->assertStringContainsString('signature', $form);
        $this->assertStringContainsString('sandbox.payfast.co.za', $form);
    }

    public function testCreateFormAutoSubmit(): void
    {
        $client = new PayFast($this->config);
        $client->makePaymentWithAForm($this->input);

        $this->assertStringContainsString('<button', $client->createForm());
    }

    public function testMakePaymentWithAFormReturnsRequest(): void
    {
        $client = new PayFast($this->config);
        $client->makePaymentWithAForm($this->input);

        $this->assertInstanceOf(PayFastRequest::class, $client->getRequest());
    }

    public function testAmountIsFormattedToTwoDecimals(): void
    {
        $client = new PayFast($this->config);
        $client->makePaymentWithAForm(array_merge($this->input, ['amount' => 50]));

        $this->assertSame('50.00', $client->getRequest()->amount);
    }

    public function testEmailConfirmationIsNormalisedToInt(): void
    {
        $client = new PayFast($this->config);
        $client->makePaymentWithAForm($this->input);

        $this->assertSame(1, $client->getRequest()->emailConfirmation);
    }

    public function testProductionUrlUsedWhenEnvIsProduction(): void
    {
        $config = array_merge($this->config, ['env' => 'production']);
        $client = new PayFast($config);
        $client->makePaymentWithAForm($this->input);

        $this->assertStringContainsString('www.payfast.co.za', $client->createForm());
    }
}
