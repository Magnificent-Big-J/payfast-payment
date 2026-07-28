<?php

namespace rainwaves\PayfastPayment\Support;

use InvalidArgumentException;

final class Money
{
    private string $amount;

    private function __construct(string $amount)
    {
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $amount)) {
            throw new InvalidArgumentException('PayFast money amounts must be positive decimal strings.');
        }

        $this->amount = number_format((float) $amount, 2, '.', '');
    }

    public static function zar(string $amount): self
    {
        return new self($amount);
    }

    public function toDecimal(): string
    {
        return $this->amount;
    }

    public function toCents(): int
    {
        return (int) round(((float) $this->amount) * 100);
    }

    public function __toString(): string
    {
        return $this->toDecimal();
    }
}

