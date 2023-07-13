<?php

namespace rainwaves\PayfastPayment;

use rainwaves\PayfastPayment\Client\PayFastSubscriptionClient;
use rainwaves\PayfastPayment\Contract\PayFastSubscriptionInterface;
use rainwaves\PayfastPayment\Model\Route;
use rainwaves\PayfastPayment\Request\PayFastSubscriptionRequest;
use stdClass;

class PayFastSubscription implements PayFastSubscriptionInterface
{
    protected stdClass $config;
    private PayFastSubscriptionInterface $payFastSubscription;
    public function __construct(array $config)
    {
        $config['url'] = Route::getUrl($config['env']);
        $this->config = (object)$config;
        $this->payFastSubscription = new PayFastSubscriptionClient($this->config);
    }
    public function createForm(): string
    {
        return $this->payFastSubscription->createForm();
    }

    public function createSubscriptionWithAForm(array $input): PayFastSubscriptionClient
    {
        return $this->payFastSubscription->createSubscriptionWithAForm($input);
    }

    public function getRequest(): PayFastSubscriptionRequest
    {
        return $this->payFastSubscription->getRequest();
    }
}