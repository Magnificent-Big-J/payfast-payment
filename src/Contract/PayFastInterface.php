<?php

namespace rainwaves\PayfastPayment\Contract;

use rainwaves\PayfastPayment\Contracts\FormInterface;

interface PayFastInterface extends FormInterface
{
    public function createForm(): string;

}