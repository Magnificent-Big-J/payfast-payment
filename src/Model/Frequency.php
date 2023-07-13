<?php

namespace rainwaves\PayfastPayment\Model;

class Frequency
{
    public const DAILY = 1;
    public const WEEKLY = 2;
    public const MONTHLY = 3;
    public const QUARTERLY = 4;
    public const BI_ANNUAL = 5;
    public const ANNUAL = 6;

    public static array $frequencyTexts = [
      self::DAILY => 'Daily',
      self::WEEKLY => 'Weekly',
      self::MONTHLY => 'Monthly',
      self::QUARTERLY => 'Quarterly',
      self::BI_ANNUAL  => 'Biannual',
      self::ANNUAL => 'Annual',
    ];

    public static function getFrequencyText(int $frequency)
    {
        return self::$frequencyTexts[$frequency];
    }
}
