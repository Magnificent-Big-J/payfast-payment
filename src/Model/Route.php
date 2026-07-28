<?php

namespace rainwaves\PayfastPayment\Model;

use rainwaves\PayfastPayment\Exception\PayFastException;
use rainwaves\PayfastPayment\Support\Environment;
use rainwaves\PayfastPayment\Support\RouteResolver;

class Route
{
    private const LOCAL      = 'local';
    private const SANDBOX    = 'sandbox';
    private const PRODUCTION = 'production';

    private static array $sites = [
        self::LOCAL      => 'https://sandbox.payfast.co.za/eng/process',
        self::SANDBOX    => 'https://sandbox.payfast.co.za/eng/process',
        self::PRODUCTION => 'https://www.payfast.co.za/eng/process',
    ];

    public static function getUrl(string $env): string
    {
        return (new RouteResolver())->checkoutUrl(Environment::normalize($env));
    }

    public static function getValidationUrl(string $env): string
    {
        return (new RouteResolver())->validationUrl(Environment::normalize($env));
    }
}
