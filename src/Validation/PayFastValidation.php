<?php

namespace rainwaves\PayfastPayment\Validation;

use rainwaves\PayfastPayment\Exception\PayFastValidationException;
use rainwaves\PayfastPayment\Model\Frequency;
use rainwaves\PayfastPayment\Model\PaymentMethod;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class PayFastValidation
{
    private const MAX_AMOUNT      = 999999.99;
    private const MAX_ITEM_NAME   = 255;
    private const MAX_DESCRIPTION = 255;
    private const MAX_CUSTOM_STR  = 255;

    public static function validate(array $input): void
    {
        try {
            v::key('amount', v::numericVal()->positive()->max(self::MAX_AMOUNT))
                ->key('item_name', v::notEmpty()->stringType()->length(1, self::MAX_ITEM_NAME))
                ->key('email_address', v::email(), false)
                ->key('payment_method', v::in(PaymentMethod::validMethods()), false)
                ->key('item_description', v::stringType()->length(0, self::MAX_DESCRIPTION), false)
                ->key('custom_str1', v::stringType()->length(0, self::MAX_CUSTOM_STR), false)
                ->key('custom_str2', v::stringType()->length(0, self::MAX_CUSTOM_STR), false)
                ->key('custom_str3', v::stringType()->length(0, self::MAX_CUSTOM_STR), false)
                ->key('custom_str4', v::stringType()->length(0, self::MAX_CUSTOM_STR), false)
                ->key('custom_str5', v::stringType()->length(0, self::MAX_CUSTOM_STR), false)
                ->assert($input);
        } catch (NestedValidationException $e) {
            throw PayFastValidationException::withErrors($e->getMessages());
        }
    }

    public static function validateSubscription(array $input): void
    {
        try {
            v::key('amount', v::numericVal()->positive()->max(self::MAX_AMOUNT))
                ->key('item_name', v::notEmpty()->stringType()->length(1, self::MAX_ITEM_NAME))
                ->key('billing_date', v::date('Y-m-d'))
                ->key('recurring_amount', v::numericVal()->positive()->max(self::MAX_AMOUNT))
                ->key('frequency', v::intVal()->in(Frequency::VALID_VALUES))
                ->key('email_address', v::email(), false)
                ->key('payment_method', v::in(PaymentMethod::validMethods()), false)
                ->key('item_description', v::stringType()->length(0, self::MAX_DESCRIPTION), false)
                ->key('custom_str1', v::stringType()->length(0, self::MAX_CUSTOM_STR), false)
                ->key('custom_str2', v::stringType()->length(0, self::MAX_CUSTOM_STR), false)
                ->key('custom_str3', v::stringType()->length(0, self::MAX_CUSTOM_STR), false)
                ->key('custom_str4', v::stringType()->length(0, self::MAX_CUSTOM_STR), false)
                ->key('custom_str5', v::stringType()->length(0, self::MAX_CUSTOM_STR), false)
                ->assert($input);
        } catch (NestedValidationException $e) {
            throw PayFastValidationException::withErrors($e->getMessages());
        }
    }
}
