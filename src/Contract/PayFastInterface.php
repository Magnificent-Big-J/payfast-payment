<?php

namespace rainwaves\PayfastPayment\Contract;

use rainwaves\PayfastPayment\Client\PayFastClient;

interface PayFastInterface extends FormInterface
{
    public function makePaymentWithAForm(array $input): PayFastClient ;

}