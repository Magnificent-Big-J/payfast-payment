<?php

namespace rainwaves\PayfastPayment\Model;

class PaymentMethod
{
    public const EFT = 'eft';
    public const CREDIT_CARD = 'cc';
    public const DEBIT_CARD = 'dc';
    public const BITCOIN = 'bc';
    public const MASTERPASS = 'mp';
    public const MOBICRED = 'mc';
    public const CASH_DEPOSIT = 'cd';
    public const SCODE = 'sc';

    public static array $paymentMethodTexts = [
      self::EFT => 'Eft payment method',
      self::CREDIT_CARD => 'Credit card payment method',
      self::DEBIT_CARD => 'Debit card payment method',
      self::BITCOIN => 'Bitcoin payment method',
      self::MASTERPASS => 'Masterpass payment method',
      self::MOBICRED => 'Mobicred payment method',
      self::CASH_DEPOSIT => 'Cash deposit payment method',
      self::SCODE => 'SCode payment method',
    ];

    public static function getPaymentText(string $type): string
    {
        return self::$paymentMethodTexts[$type];
    }
}