<?php

namespace rainwaves\PayfastPayment\Http;

final class HttpResponse
{
    public function __construct(
        private int $statusCode,
        private array $headers,
        private string $body
    ) {
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function body(): string
    {
        return $this->body;
    }
}

