<?php

namespace rainwaves\PayfastPayment\Result;

interface PayFastResult
{
    public function successful(): bool;

    public function statusCode(): int;

    public function providerCode(): ?string;

    public function providerMessage(): ?string;

    public function data(): array;
}

