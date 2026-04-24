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
        $this->mPaymentId      = $response['m_payment_id'] ?? null;
        $this->pfPaymentId     = (int) ($response['pf_payment_id'] ?? 0);
        $this->paymentStatus   = $response['payment_status'] ?? '';
        $this->itemName        = $response['item_name'] ?? '';
        $this->itemDescription = $response['item_description'] ?? null;
        $this->amountGross     = isset($response['amount_gross']) ? (float) $response['amount_gross'] : null;
        $this->amountFee       = isset($response['amount_fee'])   ? (float) $response['amount_fee']   : null;
        $this->amountNet       = isset($response['amount_net'])   ? (float) $response['amount_net']   : null;
        $this->customStr1      = $response['custom_str1'] ?? null;
        $this->customStr2      = $response['custom_str2'] ?? null;
        $this->customStr3      = $response['custom_str3'] ?? null;
        $this->customStr4      = $response['custom_str4'] ?? null;
        $this->customStr5      = $response['custom_str5'] ?? null;
        $this->customInt1      = isset($response['custom_int1']) ? (int) $response['custom_int1'] : null;
        $this->customInt2      = isset($response['custom_int2']) ? (int) $response['custom_int2'] : null;
        $this->customInt3      = isset($response['custom_int3']) ? (int) $response['custom_int3'] : null;
        $this->customInt4      = isset($response['custom_int4']) ? (int) $response['custom_int4'] : null;
        $this->customInt5      = isset($response['custom_int5']) ? (int) $response['custom_int5'] : null;
        $this->nameFirst       = $response['name_first'] ?? null;
        $this->nameLast        = $response['name_last'] ?? null;
        $this->emailAddress    = $response['email_address'] ?? null;
        $this->merchantId      = (int) ($response['merchant_id'] ?? 0);
        $this->signature       = $response['signature'] ?? '';
    }

    public function isComplete(): bool
    {
        return strtoupper($this->paymentStatus) === 'COMPLETE';
    }
}
