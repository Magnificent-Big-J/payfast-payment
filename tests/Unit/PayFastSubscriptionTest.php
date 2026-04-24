<?php

namespace rainwaves\PayfastPayment\Tests\Unit;

use Orchestra\Testbench\TestCase;
use rainwaves\PayfastPayment\Exception\PayFastValidationException;
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
            'amount'           => 100.00,
            'item_name'        => 'Test Subscription',
            'name_first'       => 'First Name',
            'name_last'        => 'Last Name',
            'email_address'    => 'test@test.com',
            'm_payment_id'     => '1234',
            'billing_date'     => date('Y-m-d', strtotime('+3 days')),
            'recurring_amount' => 100.00,
            'frequency'        => Frequency::MONTHLY,
        ];
    }

    public function testCreateFormContainsExpectedFields(): void
    {
        $client = new PayFastSubscription($this->config);
        $client->createSubscriptionWithAForm($this->input);
        $form = $client->createForm();

        $this->assertStringContainsString('amount', $form);
        $this->assertStringContainsString('Test Subscription', $form);
        $this->assertStringContainsString('subscription_type', $form);
        $this->assertStringContainsString('frequency', $form);
        $this->assertStringContainsString('signature', $form);
    }

    public function testCreateSubscriptionWithAFormReturnsRequest(): void
    {
        $client = new PayFastSubscription($this->config);
        $client->createSubscriptionWithAForm($this->input);

        $request = $client->getRequest();

        $this->assertInstanceOf(PayFastSubscriptionRequest::class, $request);
        $this->assertSame('Test Subscription', $request->itemName);
        $this->assertSame('100.00', $request->recurringAmount);
        $this->assertSame(Frequency::MONTHLY, $request->frequency);
    }

    public function testRecurringAmountIsFormattedToTwoDecimals(): void
    {
        $client = new PayFastSubscription($this->config);
        $client->createSubscriptionWithAForm(array_merge($this->input, ['recurring_amount' => 49.9]));

        $this->assertSame('49.90', $client->getRequest()->recurringAmount);
    }

    public function testInvalidFrequencyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $client = new PayFastSubscription($this->config);
        $client->createSubscriptionWithAForm(array_merge($this->input, ['frequency' => 99]));
    }

    public function testInvalidBillingDateFormatThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $client = new PayFastSubscription($this->config);
        $client->createSubscriptionWithAForm(array_merge($this->input, ['billing_date' => '13/01/2026']));
    }

    public function testSubscriptionNotifyFlagsAreNormalised(): void
    {
        $client = new PayFastSubscription($this->config);
        $client->createSubscriptionWithAForm(array_merge($this->input, [
            'subscription_notify_email'   => true,
            'subscription_notify_webhook' => false,
            'subscription_notify_buyer'   => true,
        ]));

        $request = $client->getRequest();
        $this->assertSame(1, $request->subscriptionNotifyEmail);
        $this->assertSame(0, $request->subscriptionNotifyWebhook);
        $this->assertSame(1, $request->subscriptionNotifyBuyer);
    }
}
