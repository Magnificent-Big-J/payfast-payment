<?php

namespace rainwaves\PayfastPayment\Support;

use rainwaves\PayfastPayment\Contract\ClockInterface;

final class SystemClock implements ClockInterface
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', new \DateTimeZone('Africa/Johannesburg'));
    }
}

