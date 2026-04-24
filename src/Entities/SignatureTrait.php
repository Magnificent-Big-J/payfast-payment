<?php

namespace rainwaves\PayfastPayment\Entities;

use rainwaves\PayfastPayment\Model\Sequence;
use rainwaves\PayfastPayment\Support\PayFastSignatureHelper;

trait SignatureTrait
{
    protected function generateSignature(array $data, ?string $passPhrase = null): string
    {
        if ($passPhrase !== null && $passPhrase !== '') {
            $data['passphrase'] = $passPhrase;
        }

        $fields = [];
        foreach (Sequence::$sequenceOrder as $key) {
            if (array_key_exists($key, $data) && $this->shouldInclude($data[$key])) {
                $fields[$key] = $this->normalizeValue($data[$key]);
            }
        }

        return md5(PayFastSignatureHelper::buildQuery($fields));
    }

    private function shouldInclude($value): bool
    {
        return !($value === null || $value === '');
    }

    private function normalizeValue($value): string
    {
        return trim((string) $value);
    }
}
