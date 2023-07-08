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
    public float $amount;
    public string $itemName;
    public ?string $itemDescription = null;
    public ?string $customInt = null;
    public ?string $customStr = null;
    public ?bool $emailConfirmation = false;
    public ?string $confirmationAddress = null;

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
        $this->amount = number_format( sprintf( '%.2f', $input->amount ), 2, '.', '' );
        $this->itemName = $input->item_name;
        $this->itemDescription = $input->item_description ?? $this->itemDescription;
        $this->customInt = $input->custom_int ?? $this->customInt;
        $this->customStr = $input->custom_str ?? $this->customStr;
        $this->emailConfirmation = ($input->email_confirmation ?? $this->emailConfirmation) ? 1: 0;
        $this->confirmationAddress = $input->confirmation_aSddress ?? $this->confirmationAddress;
    }

}