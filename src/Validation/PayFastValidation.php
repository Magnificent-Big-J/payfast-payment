<?php

namespace rainwaves\PayfastPayment\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class PayFastValidation
{
    public static function validate(array $input): void
    {
        try {
            v::key('amount', v::numericVal()->positive())
                ->key('item_name', v::notEmpty())
                ->assert($input);
        } catch (NestedValidationException $e) {
            $errors = $e->getMessages();
            throw new \InvalidArgumentException('Invalid input data: ' . implode(' ', $errors));
        }
    }

    public static function validateSubscription(array $input): void
    {
        try {
            v::key('amount', v::numericVal()->positive())
                ->key('item_name', v::notEmpty())
                ->key('billing_date', v::notEmpty())
                ->key('recurring_amount', v::numericVal()->positive())
                ->key('frequency', v::notEmpty())
                ->assert($input);
        } catch (NestedValidationException $e) {
            $errors = $e->getMessages();
            throw new \InvalidArgumentException('Invalid input data: ' . implode(' ', $errors));
        }
    }
}
