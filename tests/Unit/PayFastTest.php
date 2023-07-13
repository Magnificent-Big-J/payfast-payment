<?php

namespace rainwaves\PayfastPayment\Tests;

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
        $this->config = array('merchant_id' => 10000100,
            'merchant_key'=> env('MERCHANT_KEY', '46f0cd694581a'),
            'env'=> 'local',
            'return_url'=> 'https://www.example.com/success',
            'cancel_url'=> 'https://www.example.com/cancel',
            'notify_url'=> 'https://www.example.com/notify',
            'pass_phrase' => 'jt7NOE43FZPn',
        );
        $this->input = array(
            'amount' => 100.00,
            'item_name' => 'Test Product',
            'name_first' => 'First Name',
            'name_last'  => 'Last Name',
            'email_address'=> 'test@test.com',
            'm_payment_id' => '1234',
            'email_confirmation' => true,
        );
    }

    /**
     * @test
     */
    public function testCreateForm()
    {
        $client = new PayFast($this->config);
        $client->makePaymentWithAForm($this->input);
        $this->assertStringContainsString('amount', $client->createForm());
        $this->assertStringContainsString('Test Product', $client->createForm());
    }

    /**
     * @test
     */
    public function testMakePaymentWithAForm()
    {
        $client = new PayFast($this->config);
        $client->makePaymentWithAForm($this->input);

        $request = $client->getRequest();

        $this->assertInstanceOf(PayFastRequest::class, $request);
    }
}