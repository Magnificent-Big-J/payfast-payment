<?php

namespace rainwaves\PayfastPayment\Security;

final class Redactor
{
    private const SENSITIVE_KEYS = [
        'merchant_key',
        'merchant-key',
        'pass_phrase',
        'passphrase',
        'signature',
        'authorization',
    ];

    public function redact(array $data): array
    {
        $redacted = [];

        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), self::SENSITIVE_KEYS, true)) {
                $redacted[$key] = '[redacted]';
                continue;
            }

            if (is_array($value)) {
                $redacted[$key] = $this->redact($value);
                continue;
            }

            $redacted[$key] = $this->redactTokenLikeValue($value);
        }

        return $redacted;
    }

    private function redactTokenLikeValue($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        if (preg_match('/^[a-f0-9-]{24,}$/i', $value) === 1) {
            return substr($value, 0, 6) . '...' . substr($value, -4);
        }

        return $value;
    }
}

