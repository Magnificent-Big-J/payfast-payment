<?php

namespace rainwaves\PayfastPayment\Feature;

use Orchestra\Testbench\TestCase;
use rainwaves\PayfastPayment\Validation\PayFastValidation;

class PayFastValidationTest extends TestCase
{
    /**
     * @test
     */
    public function testValidationPassesWithValidInput()
    {
        $input = [
            'amount' => 100,
            'item_name' => 'Test Product'
        ];

        $this->expectNotToPerformAssertions();

        PayFastValidation::validate($input);
    }

    /**
     * @test
     */
    public function testValidationFailsWithMissingFields()
    {
        $input = [
            // missing 'amount' field
            'item_name' => 'Test Product'
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid input data: amount must be present');

        PayFastValidation::validate($input);
    }

    /**
     * @test
     */
    public function testValidationFailsWithEmptyFields()
    {
        $input = [
            'amount' => '',
            'item_name' => 'Test Product'
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid input data: amount must be positive');

        PayFastValidation::validate($input);
    }

    /**
     * @test
     */
    public function testSubscriptionValidationFailsWithMissingFields()
    {
        $input = [
            'amount' => 100,
            'item_name' => 'Test Subscription'
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid input data: billing_date must be present');

        PayFastValidation::validateSubscription($input);
    }
}
