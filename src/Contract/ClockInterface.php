<?php

namespace rainwaves\PayfastPayment\Contract;

interface ClockInterface
{
    public function now(): \DateTimeImmutable;
}

