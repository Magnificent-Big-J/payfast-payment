<?php

namespace rainwaves\PayfastPayment\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class PayFastValidation
{
    public static function validate(array $input): void
    {
        try {
            v::key('amount', v::notEmpty())
                ->key('item_name', v::notEmpty())
                ->assert($input);
        } catch (NestedValidationException $e) {
            $errors = $e->getMessages();
            throw new \InvalidArgumentException('Invalid input data: ' . implode(' ', $errors));
        }
    }
}