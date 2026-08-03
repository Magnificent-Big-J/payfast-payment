<?php

namespace rainwaves\PayfastPayment\Itn;

/**
 * Validates that an incoming ITN request originates from a genuine PayFast server.
 *
 * v2.0.0 and earlier hardcoded static CIDR ranges here, sourced from a single
 * read of https://developers.payfast.co.za/docs#notify-itn. That went stale:
 * a real sandbox ITN was later observed arriving from 144.126.193.139, an IP
 * absent from either hardcoded range. Re-checking the live docs page turned
 * up two things: (1) PayFast's own IP list had grown to include
 * 144.126.193.139 and two more /28 blocks the old constants never had, and
 * (2) PayFast's own reference validation code (embedded in that same docs
 * page, PHP/Node examples under "Check valid Payfast domain") doesn't use a
 * static list at all -- it resolves four hostnames via DNS at request time
 * and checks the incoming IP against whatever they currently resolve to.
 * Confirmed live: `w1w.payfast.co.za`/`w2w.payfast.co.za` currently resolve
 * to IPs spanning multiple providers/ASNs (including one on AWS not present
 * in the static doc list at all), so PayFast's real serving infrastructure
 * genuinely rotates -- any hardcoded list here will drift out of date again.
 * This class now mirrors PayFast's own reference approach as the primary
 * check.
 *
 * DNS introduces a failure mode the old static list never had: these records
 * carry a short TTL (~40-60s observed live), so a validation this class never
 * caches means every ITN triggers a fresh lookup, and a transient outbound-DNS
 * outage would otherwise reject every ITN during that window. Guarded against
 * below with FALLBACK_RANGES -- used only when live resolution of every one
 * of the four hostnames comes back completely empty, not as a routine path.
 */
final class PayFastIpValidator
{
    /**
     * The hostnames PayFast's own reference implementation resolves and
     * checks against -- deliberately not split by sandbox/production:
     * PayFast's reference code checks all four regardless of environment,
     * and sandbox ITN traffic has been observed arriving from IPs that
     * `w1w`/`w2w` (not `sandbox`) resolve to. $sandboxMode is kept on the
     * public methods below purely for source compatibility with existing
     * callers; it no longer changes which hosts are checked.
     */
    private const VALID_HOSTS = [
        'www.payfast.co.za',
        'sandbox.payfast.co.za',
        'w1w.payfast.co.za',
        'w2w.payfast.co.za',
    ];

    /**
     * Last-resort fallback, used only when live DNS resolution of every one
     * of VALID_HOSTS fails outright (empty result for all four -- treated as
     * "DNS itself is unreachable right now", not "this specific IP is
     * unknown"). Sourced from the same live docs page as the class docblock
     * above, current as of 2026-08-03: https://developers.payfast.co.za/docs#notify-itn.
     * This is deliberately not the primary check -- it's a narrower net than
     * live DNS (won't pick up new IPs PayFast adds without a code change,
     * same staleness risk the DNS rewrite exists to avoid) but it's better
     * than rejecting every ITN during a transient DNS blip.
     */
    private const FALLBACK_RANGES = [
        '197.97.145.144/28',
        '41.74.179.192/27',
        '102.216.36.0/28',
        '102.216.36.128/28',
    ];

    private const FALLBACK_IPS = [
        '144.126.193.139',
    ];

    /**
     * Test-only DNS override -- lets unit tests validate against a fixed,
     * offline hostname/IP map instead of depending on live network access or
     * PayFast's actual DNS records never changing mid-test-run.
     *
     * @var (callable(string): string[])|null
     */
    private static $resolverOverride = null;

    public static function isValid(string $remoteIp, bool $sandboxMode = false): bool
    {
        $resolvedIps = self::resolveValidIps();

        if ($resolvedIps !== []) {
            return in_array($remoteIp, $resolvedIps, true);
        }

        return self::isValidAgainstFallback($remoteIp);
    }

    /**
     * @return string[] the IPs PayFast's validation hostnames currently resolve to,
     *                   or the static fallback ranges/IPs if DNS resolution failed entirely
     */
    public static function getAllowedRanges(bool $sandboxMode = false): array
    {
        $resolvedIps = self::resolveValidIps();

        return $resolvedIps !== [] ? $resolvedIps : array_merge(self::FALLBACK_RANGES, self::FALLBACK_IPS);
    }

    /**
     * @param callable(string): string[] $resolver receives a hostname, returns its IPs
     */
    public static function fakeResolver(callable $resolver): void
    {
        self::$resolverOverride = $resolver;
    }

    public static function resetResolver(): void
    {
        self::$resolverOverride = null;
    }

    /**
     * @return string[]
     */
    private static function resolveValidIps(): array
    {
        $resolve = self::$resolverOverride ?? static function (string $hostname): array {
            $ips = @gethostbynamel($hostname);

            return $ips === false ? [] : $ips;
        };

        $ips = [];

        foreach (self::VALID_HOSTS as $hostname) {
            $ips = array_merge($ips, $resolve($hostname));
        }

        return array_values(array_unique($ips));
    }

    private static function isValidAgainstFallback(string $remoteIp): bool
    {
        if (in_array($remoteIp, self::FALLBACK_IPS, true)) {
            return true;
        }

        foreach (self::FALLBACK_RANGES as $cidr) {
            if (self::ipInCidr($remoteIp, $cidr)) {
                return true;
            }
        }

        return false;
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
