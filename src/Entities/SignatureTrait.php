<?php

namespace rainwaves\PayfastPayment\Entities;

use rainwaves\PayfastPayment\Model\Sequence;

trait SignatureTrait
{
    protected function generateSignature(array $data, $passPhrase = null): string
    {
        $fields = array();
        if ($passPhrase !== null && $passPhrase !== '') {
            $data['passphrase'] = $passPhrase;
        }

        foreach (Sequence::$sequenceOrder as $key) {
            if (array_key_exists($key, $data) && $this->shouldInclude($data[$key])) {
                $fields[$key] = $this->normalizeValue($data[$key]);
            }
        }

        return md5($this->buildQuery($fields));
    }

    private function shouldInclude($value): bool
    {
        return !($value === null || $value === '');
    }

    private function normalizeValue($value): string
    {
        return trim((string) $value);
    }

    private function buildQuery(array $fields): string
    {
        $query = http_build_query($fields, '', '&', PHP_QUERY_RFC1738);
        return preg_replace_callback('/%[0-9a-f]{2}/', function ($match) {
            return strtoupper($match[0]);
        }, $query);
    }
}
