<?php

namespace rainwaves\PayfastPayment;

use stdClass;

class PayFast
{
    private const LOCAL = 'local';
    private const PRODUCTION = 'production';
    protected stdClass $config;

    private static array $sites = [
      self::LOCAL => 'https://www.payfast.co.za/eng/process',
      self::PRODUCTION => 'https://sandbox.payfast.co.za​/eng/process',
    ];

    public function __construct(array $config)
    {
        $config['url'] = self::$sites[$config['env']];
        $this->config = (object)$config;
    }
}