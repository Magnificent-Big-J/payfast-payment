<?php

namespace rainwaves\PayfastPayment;

use rainwaves\PayfastPayment\Client\PayFastClient;
use rainwaves\PayfastPayment\Contract\PayFastInterface;
use stdClass;

class PayFast implements PayFastInterface
{
    private const LOCAL = 'local';
    private const PRODUCTION = 'production';
    protected stdClass $config;

    private static array $sites = [
      self::LOCAL => 'https://sandbox.payfast.co.za/eng/process' ,
      self::PRODUCTION => 'https://www.payfast.co.za/eng/process',
    ];

    private PayFastClient $payFastClient;
    public function __construct(array $config)
    {
        $config['url'] = self::$sites[$config['env']];
        $this->config = (object)$config;
        $this->payFastClient = new PayFastClient($this->config);
    }

    public function createForm(): string
    {
        return  $this->payFastClient->createForm();
    }

    public function makePaymentWithAForm(array $input): PayFastClient
    {
        $this->payFastClient->makePaymentWithAForm($input);

        return  $this->payFastClient;
    }
}