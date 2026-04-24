<?php

namespace rainwaves\PayfastPayment\Contract;

use rainwaves\PayfastPayment\Client\PayFastClient;
use rainwaves\PayfastPayment\Request\PayFastRequest;

interface PayFastInterface extends FormInterface
{
    public function makePaymentWithAForm(array $input): PayFastClient;

    public function getRequest(): PayFastRequest;
}
