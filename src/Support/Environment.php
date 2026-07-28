<?php

namespace rainwaves\PayfastPayment\Support;

use rainwaves\PayfastPayment\Exception\PayFastException;

final class Environment
{
    public const SANDBOX = 'sandbox';
    public const PRODUCTION = 'production';

    public static function normalize(string $environment): string
    {
        $environment = strtolower(trim($environment));

        if (in_array($environment, ['local', 'test', 'testing', 'sandbox'], true)) {
            return self::SANDBOX;
        }

        if (in_array($environment, ['live', 'prod', 'production'], true)) {
            return self::PRODUCTION;
        }

        throw PayFastException::invalidEnvironment($environment, [self::SANDBOX, self::PRODUCTION, 'local']);
    }

    public static function isSandbox(string $environment): bool
    {
        return self::normalize($environment) === self::SANDBOX;
    }
}

