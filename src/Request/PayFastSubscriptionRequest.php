<?php

namespace rainwaves\PayfastPayment\Request;

class PayFastSubscriptionRequest extends PayFastRequest
{
    public int $subscriptionType = 1;
    public string $billingDate;
    public string $recurringAmount;
    public int $frequency;
    public int $cycles = 0;
    public ?int $subscriptionNotifyEmail = null;
    public ?int $subscriptionNotifyWebhook = null;
    public ?int $subscriptionNotifyBuyer = null;
    public function __construct(\stdClass $input, \stdClass $config)
    {
        parent::__construct($input, $config);
        $this->subscriptionType = $input->subscription_type ?? $this->subscriptionType;
        $this->billingDate = $input->billing_date;
        $this->recurringAmount = sprintf('%.2f', (float) $input->recurring_amount);
        $this->frequency = (int) $input->frequency;
        $this->cycles = $input->cycles ?? $this->cycles;
        if (property_exists($input, 'subscription_notify_email')) {
            $this->subscriptionNotifyEmail = $input->subscription_notify_email ? 1 : 0;
        }
        if (property_exists($input, 'subscription_notify_webhook')) {
            $this->subscriptionNotifyWebhook = $input->subscription_notify_webhook ? 1 : 0;
        }
        if (property_exists($input, 'subscription_notify_buyer')) {
            $this->subscriptionNotifyBuyer = $input->subscription_notify_buyer ? 1 : 0;
        }
    }

}
