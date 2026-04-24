<?php

namespace rainwaves\PayfastPayment\Model;

class Frequency
{
    public const DAILY     = 1;
    public const WEEKLY    = 2;
    public const MONTHLY   = 3;
    public const QUARTERLY = 4;
    public const BI_ANNUAL = 5;
    public const ANNUAL    = 6;

    public const VALID_VALUES = [
        self::DAILY,
        self::WEEKLY,
        self::MONTHLY,
        self::QUARTERLY,
        self::BI_ANNUAL,
        self::ANNUAL,
    ];

    public static array $frequencyTexts = [
        self::DAILY     => 'Daily',
        self::WEEKLY    => 'Weekly',
        self::MONTHLY   => 'Monthly',
        self::QUARTERLY => 'Quarterly',
        self::BI_ANNUAL => 'Biannual',
        self::ANNUAL    => 'Annual',
    ];

    public static function isValid(int $frequency): bool
    {
        return in_array($frequency, self::VALID_VALUES, true);
    }

    public static function getFrequencyText(int $frequency): string
    {
        if (!self::isValid($frequency)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid frequency %d. Valid values: %s.',
                $frequency,
                implode(', ', self::VALID_VALUES)
            ));
        }

        return self::$frequencyTexts[$frequency];
    }
}
