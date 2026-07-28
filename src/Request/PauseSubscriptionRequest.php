<?php

namespace rainwaves\PayfastPayment\Request;

use InvalidArgumentException;

final class PauseSubscriptionRequest
{
    public function __construct(private int $cycles = 1)
    {
        if ($cycles < 1) {
            throw new InvalidArgumentException('Pause cycles must be at least 1.');
        }
    }

    public function toArray(): array
    {
        return ['cycles' => $this->cycles];
    }
}

