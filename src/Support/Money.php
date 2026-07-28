<?php

namespace rainwaves\PayfastPayment\Support;

use InvalidArgumentException;

final class Money
{
    private string $amount;
    private int $cents;

    private function __construct(string $amount)
    {
        $amount = trim($amount);

        if (!preg_match('/^(0|[1-9]\d*)(\.\d{1,2})?$/', $amount)) {
            throw new InvalidArgumentException('PayFast money amounts must be positive decimal strings.');
        }

        [$rands, $cents] = array_pad(explode('.', $amount, 2), 2, '');
        $cents = str_pad($cents, 2, '0');
        $this->cents = ((int) $rands * 100) + (int) $cents;

        if ($this->cents <= 0) {
            throw new InvalidArgumentException('PayFast money amounts must be greater than zero.');
        }

        $this->amount = $rands . '.' . $cents;
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
        return $this->cents;
    }

    public function __toString(): string
    {
        return $this->toDecimal();
    }
}
