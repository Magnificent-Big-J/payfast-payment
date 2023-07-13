<?php

namespace rainwaves\PayfastPayment\Model;

class Route
{
    private const LOCAL = 'local';
    private const PRODUCTION = 'production';
    private static array $sites = [
        self::LOCAL => 'https://sandbox.payfast.co.za/eng/process' ,
        self::PRODUCTION => 'https://www.payfast.co.za/eng/process',
    ];

    public static function getUrl(string $env)
    {
        return self::$sites[$env];
    }

}