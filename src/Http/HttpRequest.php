<?php

namespace rainwaves\PayfastPayment\Http;

final class HttpRequest
{
    public function __construct(
        private string $method,
        private string $url,
        private array $headers = [],
        private array $body = [],
        private int $connectTimeoutSeconds = 5,
        private int $timeoutSeconds = 15
    ) {
    }

    public function method(): string
    {
        return strtoupper($this->method);
    }

    public function url(): string
    {
        return $this->url;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function body(): array
    {
        return $this->body;
    }

    public function connectTimeoutSeconds(): int
    {
        return $this->connectTimeoutSeconds;
    }

    public function timeoutSeconds(): int
    {
        return $this->timeoutSeconds;
    }

    public function encodedBody(): string
    {
        return http_build_query($this->body, '', '&', PHP_QUERY_RFC1738);
    }
}

