<?php

namespace rainwaves\PayfastPayment\Request;

class PayFastSubscriptionRequest extends PayFastRequest
{
    public int $subscriptionType = 1;
    public string $billingDate;
    public float $recurringAmount;
     public int $frequency;
    public int $cycles = 0;
    public bool $subscriptionNotifyEmail;
    public bool $subscriptionNotifyWebhook;
    public bool $subscriptionNotifyBuyer;
    public function __construct(\stdClass $input, \stdClass $config)
    {
        parent::__construct($input, $config);
        $this->billingDate = $input->billing_date;
        $this->recurringAmount = $input->recurring_amount;
        $this->frequency = $input->frequency;
        $this->subscriptionNotifyEmail = $input->subscription_notify_email ?? false;
        $this->subscriptionNotifyWebhook = $input->subscription_notify_webhook ?? false;
        $this->subscriptionNotifyBuyer = $input->subscription_notify_buyer ?? false;
    }

}