<?php

namespace rainwaves\PayfastPayment\Model;

class Sequence
{
    /**
     * @var array|string[]
     */
    public static array $sequenceOrder = [
        'merchant_id',
        'merchant_key',
        'return_url',
        'cancel_url',
        'notify_url',
        'name_first',
        'name_last',
        'email_address',
        'cell_number',
        'm_payment_id',
        'amount',
        'item_name',
        'item_description',
        'custom_int1',
        'custom_int2',
        'custom_int3',
        'custom_int4',
        'custom_int5',
        'custom_str1',
        'custom_str2',
        'custom_str3',
        'custom_str4',
        'custom_str5',
        'email_confirmation',
        'confirmation_address',
        'payment_method',
        'subscription_type',
        'billing_date',
        'recurring_amount',
        'frequency',
        'cycles',
        'passphrase'
    ];

    public static function order(array $input): array
    {
        return self::correctSequence($input, self::$sequenceOrder);
    }

    private static function correctSequence(array $input, array $sequence): array
    {
        uksort($input, function ($a, $b) use ($sequence) {
            $posA = array_search($a, $sequence);
            $posB = array_search($b, $sequence);
            return $posA <=> $posB;
        });

        return $input;
    }
}