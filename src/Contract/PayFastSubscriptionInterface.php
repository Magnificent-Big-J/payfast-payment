<?php

namespace rainwaves\PayfastPayment\Contract;

use rainwaves\PayfastPayment\Client\PayFastSubscriptionClient;

interface PayFastSubscriptionInterface extends FormInterface
{
    public function createSubscriptionWithAForm(array $input): PayFastSubscriptionClient;
}