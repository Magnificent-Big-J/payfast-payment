<?php

namespace rainwaves\PayfastPayment\Contract;

use rainwaves\PayfastPayment\Request\ExpectedPayment;
use rainwaves\PayfastPayment\Result\ItnValidationResult;

interface ItnValidatorInterface
{
    public function validate(
        array $payload,
        ?string $rawBody,
        string $remoteIp,
        ExpectedPayment $expected,
        bool $confirmWithPayFast = true
    ): ItnValidationResult;
}

