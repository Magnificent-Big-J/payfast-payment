<?php

namespace rainwaves\PayfastPayment\Contract;

use rainwaves\PayfastPayment\Client\PayFastSubscriptionClient;
use rainwaves\PayfastPayment\Request\PayFastSubscriptionRequest;

interface PayFastSubscriptionInterface extends FormInterface
{
    public function createSubscriptionWithAForm(array $input): PayFastSubscriptionClient;

    public function getRequest(): PayFastSubscriptionRequest;
}
