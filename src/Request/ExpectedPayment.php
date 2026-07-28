<?php

namespace rainwaves\PayfastPayment\Request;

use rainwaves\PayfastPayment\Support\Money;

final class ExpectedPayment
{
    public function __construct(
        public readonly string $merchantId,
        public readonly Money $amount
    ) {
    }
}

