<?php

namespace rainwaves\PayfastPayment\Support;

final class PayFastSignatureHelper
{
    /**
     * Build a PayFast-compatible query string: RFC 1738-encoded with uppercase percent-encoding.
     * Shared by outbound signature generation and inbound ITN verification.
     */
    public static function buildQuery(array $fields): string
    {
        $query = http_build_query($fields, '', '&', PHP_QUERY_RFC1738);
        return preg_replace_callback('/%[0-9a-f]{2}/', static function (array $match): string {
            return strtoupper($match[0]);
        }, $query);
    }
}
