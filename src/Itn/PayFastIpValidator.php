<?php

namespace rainwaves\PayfastPayment\Itn;

/**
 * Validates that an incoming ITN request originates from a known PayFast IP range.
 *
 * IP ranges sourced from https://developers.payfast.co.za/docs#notify-itn
 * Always call this before trusting any ITN payload.
 */
final class PayFastIpValidator
{
    private const SANDBOX_RANGES = [
        '196.33.227.224/27',
    ];

    private const PRODUCTION_RANGES = [
        '197.97.145.144/28',
        '41.74.179.192/27',
    ];

    public static function isValid(string $remoteIp, bool $sandboxMode = false): bool
    {
        $ranges = $sandboxMode ? self::SANDBOX_RANGES : self::PRODUCTION_RANGES;

        foreach ($ranges as $cidr) {
            if (self::ipInCidr($remoteIp, $cidr)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public static function getAllowedRanges(bool $sandboxMode = false): array
    {
        return $sandboxMode ? self::SANDBOX_RANGES : self::PRODUCTION_RANGES;
    }

    private static function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr, 2);
        $ipLong     = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $mask = ~((1 << (32 - (int) $bits)) - 1);
        return ($ipLong & $mask) === ($subnetLong & $mask);
    }
}
