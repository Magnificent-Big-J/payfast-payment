<?php

use PHPUnit\Framework\TestCase;
use rainwaves\PayfastPayment\Model\Frequency;
use rainwaves\PayfastPayment\PayFastSubscription;
use rainwaves\PayfastPayment\Request\PayFastSubscriptionRequest;

class PayFastSubscriptionTest extends TestCase
{
    protected array $config;
    protected array $input;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = array('merchant_id' => 10000100,
            'merchant_key'=> '46f0cd694581a',
            'env'=> 'local',
            'return_url'=> 'https://www.example.com/success',
            'cancel_url'=> 'https://www.example.com/cancel',
            'notify_url'=> 'https://www.example.com/notify',
            'pass_phrase' => 'jt7NOE43FZPn',
        );
        $this->input = array(
            'amount' => 100.00,
            'item_name' => 'Test Subscription',
            'name_first' => 'First Name',
            'name_last'  => 'Last Name',
            'email_address'=> 'test@test.com',
            'm_payment_id' => '1234',
            'email_confirmation' => true,
            'billing_date' => \Carbon\Carbon::now()->addDays(3)->format('Y-m-d'),
            'recurring_amount' => 100.00,
            'frequency' => Frequency::MONTHLY,
        );
    }

    /**
     * @test
     */
    public function testCreateForm()
    {
        $client = new PayFastSubscription($this->config);
        $client->createSubscriptionWithAForm($this->input);
        $this->assertStringContainsString('amount', $client->createForm());
        $this->assertStringContainsString('Test Subscription', $client->createForm());

    }

    /**
     * @test
     */
    public function testCreateSubscriptionWithAForm()
    {
        $client = new PayFastSubscription($this->config);
        $client->createSubscriptionWithAForm($this->input);

        $request = $client->getRequest();

        $this->assertInstanceOf(PayFastSubscriptionRequest::class, $request);
        $this->assertSame('Test Subscription', $request->itemName);
        $this->assertSame('100.00', $request->recurringAmount);
    }
}
