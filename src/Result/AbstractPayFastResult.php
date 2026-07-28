<?php

namespace rainwaves\PayfastPayment\Result;

abstract class AbstractPayFastResult implements PayFastResult
{
    public function __construct(
        private int $statusCode,
        private array $payload
    ) {
    }

    public function successful(): bool
    {
        return $this->statusCode >= 200
            && $this->statusCode < 300
            && ($this->payload['status'] ?? null) !== 'failed'
            && (($this->payload['data']['response'] ?? true) !== false);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function providerCode(): ?string
    {
        return isset($this->payload['code']) ? (string) $this->payload['code'] : null;
    }

    public function providerMessage(): ?string
    {
        $message = $this->payload['data']['message'] ?? $this->payload['status'] ?? null;

        return $message === null ? null : (string) $message;
    }

    public function data(): array
    {
        $data = $this->payload['data'] ?? [];

        return is_array($data) ? $data : ['response' => $data];
    }
}

