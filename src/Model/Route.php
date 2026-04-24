<?php

namespace rainwaves\PayfastPayment\Model;

use rainwaves\PayfastPayment\Exception\PayFastException;

class Route
{
    private const LOCAL      = 'local';
    private const PRODUCTION = 'production';

    private static array $sites = [
        self::LOCAL      => 'https://sandbox.payfast.co.za/eng/process',
        self::PRODUCTION => 'https://www.payfast.co.za/eng/process',
    ];

    public static function getUrl(string $env): string
    {
        if (!array_key_exists($env, self::$sites)) {
            throw PayFastException::invalidEnvironment($env, array_keys(self::$sites));
        }

        return self::$sites[$env];
    }

    public static function getValidationUrl(string $env): string
    {
        if ($env === self::PRODUCTION) {
            return 'https://www.payfast.co.za/eng/query/validate';
        }

        return 'https://sandbox.payfast.co.za/eng/query/validate';
    }
}