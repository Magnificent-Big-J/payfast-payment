<?php

namespace rainwaves\PayfastPayment\Exception;

class PayFastSubscriptionException extends PayFastException
{
    public static function invalidBillingDate(string $date): self
    {
        return new self(sprintf(
            'Invalid billing date "%s". Expected format: Y-m-d (e.g. 2026-01-15).',
            $date
        ));
    }

    public static function invalidFrequency(int $frequency, array $valid): self
    {
        return new self(sprintf(
            'Invalid subscription frequency %d. Valid values are: %s.',
            $frequency,
            implode(', ', $valid)
        ));
    }
}