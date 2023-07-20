<?php

namespace rainwaves\PayfastPayment\Response;

use rainwaves\PayfastPayment\Abstraction\Arrayable;

class PayFastResponse extends Arrayable
{
    public ?string $mPaymentId;
    public int $pfPaymentId;
    public string $paymentStatus;
    public string $itemName;
    public ?string $itemDescription;
    public ?float $amountGross;
    public ?float $amountFee;
    public ?float $amountNet;
    public ?string $customStr1;
    public ?string $customStr2;
    public ?string $customStr3;
    public ?string $customStr4;
    public ?string $customStr5;
    public ?int $customInt1;
    public ?int $customInt2;
    public ?int $customInt3;
    public ?int $customInt4;
    public ?int $customInt5;
    public ?string $nameFirst;
    public ?string $nameLast;
    public ?string $emailAddress;
    public int $merchantId;
    public string $signature;

    public function __construct(array $response)
    {
        $this->mPaymentId = $response['m_payment_id'];
        $this->pfPaymentId = $response['pf_payment_id'];
        $this->paymentStatus = $response['payment_status'];
        $this->itemName = $response['item_name'];
        $this->itemDescription = $response['item_description'];
        $this->amountGross = $response['amount_gross'];
        $this->amountFee = $response['amount_fee'];
        $this->amountNet = $response['amount_net'];
        $this->customStr1 = $response['custom_str1'];
        $this->customStr2 = $response['custom_str2'];
        $this->customStr3 = $response['custom_str3'];
        $this->customStr4 = $response['custom_str4'];
        $this->customStr5 = $response['custom_str5'];
        $this->customInt1 = $response['custom_int1'];
        $this->customInt2 = $response['custom_int2'];
        $this->customInt3 = $response['custom_int3'];
        $this->customInt4 = $response['custom_int4'];
        $this->customInt5 = $response['custom_int5'];
        $this->nameFirst = $response['name_first'];
        $this->nameLast = $response['name_last'];
        $this->emailAddress = $response['email_address'];
        $this->merchantId = $response['merchant_id'];
        $this->signature = $response['signature'];
    }
}