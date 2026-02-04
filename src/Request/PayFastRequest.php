<?php

namespace rainwaves\PayfastPayment\Request;

use rainwaves\PayfastPayment\Abstraction\Arrayable;

class PayFastRequest extends Arrayable
{
    public int $merchantId;
    public string $merchantKey;
    public ?string $returnUrl = null;
    public ?string $notifyUrl = null;
    public ?string $cancelUrl = null;
    public ?string $nameFirst = null;
    public ?string $nameLast = null;
    public ?string $emailAddress = null;
    public ?string $cellNumber = null;
    public ?string $mPaymentId = null;
    public string $amount;
    public string $itemName;
    public ?string $itemDescription = null;
    public ?string $customInt1 = null;
    public ?string $customInt2 = null;
    public ?string $customInt3 = null;
    public ?string $customInt4 = null;
    public ?string $customInt5 = null;
    public ?string $customStr1 = null;
    public ?string $customStr2 = null;
    public ?string $customStr3 = null;
    public ?string $customStr4 = null;
    public ?string $customStr5 = null;
    public ?int $emailConfirmation = null;
    public ?string $confirmationAddress = null;
    public ?string $paymentMethod = null;

    public function __construct(\stdClass $input, \stdClass $config)
    {
        $this->merchantId = $config->merchant_id;
        $this->merchantKey = $config->merchant_key;
        $this->returnUrl = $config->return_url ?? $this->returnUrl;
        $this->notifyUrl = $config->notify_url ?? $this->notifyUrl;
        $this->cancelUrl = $config->cancel_url ?? $this->cancelUrl;
        $this->nameFirst = $input->name_first ?? $this->nameFirst;
        $this->nameLast = $input->name_last ?? $this->nameLast;
        $this->emailAddress = $input->email_address ?? $this->emailAddress;
        $this->cellNumber = $input->cell_number ?? $this->cellNumber;
        $this->mPaymentId = $input->m_payment_id ?? $this->mPaymentId;
        $this->amount = sprintf('%.2f', (float) $input->amount);
        $this->itemName = $input->item_name;
        $this->itemDescription = $input->item_description ?? $this->itemDescription;
        $this->customInt1 = $input->custom_int1 ?? ($input->custom_int ?? $this->customInt1);
        $this->customInt2 = $input->custom_int2 ?? $this->customInt2;
        $this->customInt3 = $input->custom_int3 ?? $this->customInt3;
        $this->customInt4 = $input->custom_int4 ?? $this->customInt4;
        $this->customInt5 = $input->custom_int5 ?? $this->customInt5;
        $this->customStr1 = $input->custom_str1 ?? ($input->custom_str ?? $this->customStr1);
        $this->customStr2 = $input->custom_str2 ?? $this->customStr2;
        $this->customStr3 = $input->custom_str3 ?? $this->customStr3;
        $this->customStr4 = $input->custom_str4 ?? $this->customStr4;
        $this->customStr5 = $input->custom_str5 ?? $this->customStr5;
        if (property_exists($input, 'email_confirmation')) {
            $this->emailConfirmation = $input->email_confirmation ? 1 : 0;
        }
        $this->confirmationAddress = $input->confirmation_address ?? $this->confirmationAddress;
        $this->paymentMethod = $input->payment_method ?? $this->paymentMethod;
    }

}
