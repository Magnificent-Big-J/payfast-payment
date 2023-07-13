<?php

namespace rainwaves\PayfastPayment;

use rainwaves\PayfastPayment\Client\PayFastClient;
use rainwaves\PayfastPayment\Contract\PayFastInterface;
use rainwaves\PayfastPayment\Model\Route;
use rainwaves\PayfastPayment\Request\PayFastRequest;
use stdClass;

class PayFast implements PayFastInterface
{
    protected stdClass $config;
    private PayFastClient $payFastClient;
    public function __construct(array $config)
    {
        $config['url'] = Route::getUrl($config['env']);
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
    public function getRequest(): PayFastRequest
    {
        return $this->payFastClient->getRequest();
    }

}