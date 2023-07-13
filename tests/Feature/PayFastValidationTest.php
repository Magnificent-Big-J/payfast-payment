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
        $this->expectExceptionMessage('Invalid input data: amount must not be empty');

        PayFastValidation::validate($input);
    }
}