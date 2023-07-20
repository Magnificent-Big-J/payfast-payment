<?php

namespace rainwaves\PayfastPayment\Response;

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