<?php

namespace rainwaves\PayfastPayment\Security;

final class PayloadCanonicalizer
{
    public function forApiSignature(array $headers, array $body = [], array $query = [], ?string $passPhrase = null): string
    {
        $data = array_merge($headers, $body, $query);

        if ($passPhrase !== null && $passPhrase !== '') {
            $data['passphrase'] = $passPhrase;
        }

        unset($data['signature'], $data['testing']);
        ksort($data);

        $pairs = [];
        foreach ($data as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $pairs[] = $key . '=' . str_replace('%20', '+', rawurlencode(trim((string) $value)));
        }

        return implode('&', $pairs);
    }
}

