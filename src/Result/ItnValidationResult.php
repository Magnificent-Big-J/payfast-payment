<?php

namespace rainwaves\PayfastPayment\Result;

final class ItnValidationResult
{
    public function __construct(
        private array $checks,
        private ?string $paymentId,
        private ?string $paymentStatus,
        private array $payload
    ) {
    }

    public function valid(): bool
    {
        foreach ($this->checks as $check) {
            if ($check !== true) {
                return false;
            }
        }

        return true;
    }

    public function checks(): array
    {
        return $this->checks;
    }

    public function paymentId(): ?string
    {
        return $this->paymentId;
    }

    public function paymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}

