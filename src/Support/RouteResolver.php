<?php

namespace rainwaves\PayfastPayment\Support;

final class RouteResolver
{
    public function checkoutUrl(string $environment): string
    {
        return Environment::isSandbox($environment)
            ? 'https://sandbox.payfast.co.za/eng/process'
            : 'https://www.payfast.co.za/eng/process';
    }

    public function validationUrl(string $environment): string
    {
        return Environment::isSandbox($environment)
            ? 'https://sandbox.payfast.co.za/eng/query/validate'
            : 'https://www.payfast.co.za/eng/query/validate';
    }

    public function apiBaseUrl(): string
    {
        return 'https://api.payfast.co.za';
    }

    public function subscriptionUrl(string $token, string $operation, string $environment): string
    {
        $url = sprintf('%s/subscriptions/%s/%s', $this->apiBaseUrl(), rawurlencode($token), $operation);

        if (Environment::isSandbox($environment)) {
            $url .= '?testing=true';
        }

        return $url;
    }

    public function recurringCardUpdateUrl(string $token, ?string $returnUrl, string $environment): string
    {
        $host = Environment::isSandbox($environment)
            ? 'https://sandbox.payfast.co.za'
            : 'https://www.payfast.co.za';

        $url = sprintf('%s/eng/recurring/update/%s', $host, rawurlencode($token));

        if ($returnUrl !== null && $returnUrl !== '') {
            $url .= '?return=' . rawurlencode($returnUrl);
        }

        return $url;
    }
}

