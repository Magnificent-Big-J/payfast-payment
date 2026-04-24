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
        self::EFT          => 'EFT',
        self::CREDIT_CARD  => 'Credit Card',
        self::DEBIT_CARD   => 'Debit Card',
        self::BITCOIN      => 'Bitcoin',
        self::MASTERPASS   => 'Masterpass',
        self::MOBICRED     => 'Mobicred',
        self::CASH_DEPOSIT => 'Cash Deposit',
        self::SCODE        => 'SCode',
    ];

    public static function isValid(string $method): bool
    {
        return array_key_exists($method, self::$paymentMethodTexts);
    }

    /**
     * @return string[]
     */
    public static function validMethods(): array
    {
        return array_keys(self::$paymentMethodTexts);
    }

    public static function getPaymentText(string $type): string
    {
        if (!self::isValid($type)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid payment method "%s". Valid values: %s.',
                $type,
                implode(', ', self::validMethods())
            ));
        }

        return self::$paymentMethodTexts[$type];
    }
}