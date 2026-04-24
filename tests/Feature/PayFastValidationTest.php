<?php

namespace rainwaves\PayfastPayment\Tests\Feature;

use Orchestra\Testbench\TestCase;
use rainwaves\PayfastPayment\Exception\PayFastValidationException;
use rainwaves\PayfastPayment\Validation\PayFastValidation;

class PayFastValidationTest extends TestCase
{
    // --- One-time payment validation ---

    public function testValidationPassesWithMinimalInput(): void
    {
        $this->expectNotToPerformAssertions();

        PayFastValidation::validate(['amount' => 100, 'item_name' => 'Test Product']);
    }

    public function testValidationPassesWithOptionalFields(): void
    {
        $this->expectNotToPerformAssertions();

        PayFastValidation::validate([
            'amount'         => 250.00,
            'item_name'      => 'Premium Plan',
            'email_address'  => 'customer@example.com',
            'payment_method' => 'cc',
            'custom_str1'    => 'ref-001',
        ]);
    }

    public function testValidationFailsWithMissingAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid input data: amount must be present');

        PayFastValidation::validate(['item_name' => 'Test Product']);
    }

    public function testValidationFailsWithEmptyAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid input data: amount must be positive');

        PayFastValidation::validate(['amount' => '', 'item_name' => 'Test Product']);
    }

    public function testValidationFailsWithInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PayFastValidation::validate([
            'amount'        => 100,
            'item_name'     => 'Product',
            'email_address' => 'not-a-valid-email',
        ]);
    }

    public function testValidationFailsWithInvalidPaymentMethod(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PayFastValidation::validate([
            'amount'         => 100,
            'item_name'      => 'Product',
            'payment_method' => 'bitcoin_cash',
        ]);
    }

    public function testValidationFailsWhenCustomStrExceedsMaxLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PayFastValidation::validate([
            'amount'      => 100,
            'item_name'   => 'Product',
            'custom_str1' => str_repeat('x', 256),
        ]);
    }

    public function testValidationExceptionExtendsInvalidArgumentException(): void
    {
        try {
            PayFastValidation::validate(['item_name' => 'Product']);
            $this->fail('Expected exception not thrown');
        } catch (PayFastValidationException $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
            $this->assertNotEmpty($e->getValidationErrors());
        }
    }

    // --- Subscription validation ---

    public function testSubscriptionValidationPassesWithValidInput(): void
    {
        $this->expectNotToPerformAssertions();

        PayFastValidation::validateSubscription([
            'amount'           => 100,
            'item_name'        => 'Monthly Plan',
            'billing_date'     => date('Y-m-d', strtotime('+7 days')),
            'recurring_amount' => 100,
            'frequency'        => 3,
        ]);
    }

    public function testSubscriptionValidationFailsWithMissingBillingDate(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid input data: billing_date must be present');

        PayFastValidation::validateSubscription([
            'amount'   => 100,
            'item_name' => 'Test Subscription',
        ]);
    }

    public function testSubscriptionValidationFailsWithBadDateFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PayFastValidation::validateSubscription([
            'amount'           => 100,
            'item_name'        => 'Plan',
            'billing_date'     => '01/13/2026',
            'recurring_amount' => 100,
            'frequency'        => 3,
        ]);
    }

    public function testSubscriptionValidationFailsWithInvalidFrequency(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PayFastValidation::validateSubscription([
            'amount'           => 100,
            'item_name'        => 'Plan',
            'billing_date'     => date('Y-m-d', strtotime('+7 days')),
            'recurring_amount' => 100,
            'frequency'        => 99,
        ]);
    }

    public function testSubscriptionValidationFailsWithNegativeRecurringAmount(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PayFastValidation::validateSubscription([
            'amount'           => 100,
            'item_name'        => 'Plan',
            'billing_date'     => date('Y-m-d', strtotime('+7 days')),
            'recurring_amount' => -50,
            'frequency'        => 3,
        ]);
    }
}
