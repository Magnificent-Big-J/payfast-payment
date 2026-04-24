<?php

namespace rainwaves\PayfastPayment\Exception;

class PayFastException extends \RuntimeException
{
    public static function invalidEnvironment(string $env, array $valid): self
    {
        return new self(sprintf(
            'Unknown PayFast environment "%s". Valid values are: %s.',
            $env,
            implode(', ', $valid)
        ));
    }

    public static function endpointUnreachable(string $url, string $reason): self
    {
        return new self(sprintf('PayFast validation endpoint "%s" is unreachable: %s', $url, $reason));
    }

    public static function curlNotAvailable(): self
    {
        return new self('The cURL PHP extension is required for PayFast endpoint validation.');
    }

    public static function notInitialized(string $prerequisiteMethod): self
    {
        return new self(sprintf(
            'Call %s() before calling createForm().',
            $prerequisiteMethod
        ));
    }
}
