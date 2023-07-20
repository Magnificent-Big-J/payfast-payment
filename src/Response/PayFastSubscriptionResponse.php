<?php

namespace rainwaves\PayfastPayment\Request;

class PayFastSubscriptionResponse extends PayFastResponse
{
    public string $token;
    public ?string $billingDate;
    public function __construct(array $response)
    {
        parent::__construct($response);

        $this->token = $response['token'];
        $this->billingDate = $response['billing_date'];
    }
}