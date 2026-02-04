<?php

namespace rainwaves\PayfastPayment\Itn;

use rainwaves\PayfastPayment\Response\PayFastResponse;
use rainwaves\PayfastPayment\Response\PayFastSubscriptionResponse;

class PayFastItnValidator
{
    private array $data;
    private ?string $passPhrase;

    public function __construct(array $data, ?string $passPhrase = null)
    {
        $this->data = $data;
        $this->passPhrase = $passPhrase;
    }

    public function validateSignature(): bool
    {
        if (!isset($this->data['signature'])) {
            return false;
        }

        $signature = $this->data['signature'];
        $calculated = $this->generateItnSignature();
        return hash_equals($calculated, $signature);
    }

    public function validateAmount(string $expectedAmount): bool
    {
        if (!isset($this->data['amount_gross'])) {
            return false;
        }

        $expected = sprintf('%.2f', (float) $expectedAmount);
        $actual = sprintf('%.2f', (float) $this->data['amount_gross']);
        return hash_equals($expected, $actual);
    }

    public function validateMerchantId(string $expectedMerchantId): bool
    {
        if (!isset($this->data['merchant_id'])) {
            return false;
        }

        return hash_equals((string) $expectedMerchantId, (string) $this->data['merchant_id']);
    }

    public function validateWithPayFastEndpoint(string $validateUrl): bool
    {
        $query = http_build_query($this->data, '', '&', PHP_QUERY_RFC1738);
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $query,
                'timeout' => 30,
            ],
        ];

        $context = stream_context_create($options);
        $result = @file_get_contents($validateUrl, false, $context);
        if ($result === false) {
            return false;
        }

        return trim($result) === 'VALID';
    }

    public function response(): PayFastResponse
    {
        if (isset($this->data['token'])) {
            return new PayFastSubscriptionResponse($this->data);
        }

        return new PayFastResponse($this->data);
    }

    private function generateItnSignature(): string
    {
        $data = $this->data;
        unset($data['signature']);

        if ($this->passPhrase !== null && $this->passPhrase !== '') {
            $data['passphrase'] = $this->passPhrase;
        }

        ksort($data);
        $fields = [];
        foreach ($data as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $fields[$key] = trim((string) $value);
        }

        return md5($this->buildQuery($fields));
    }

    private function buildQuery(array $fields): string
    {
        $query = http_build_query($fields, '', '&', PHP_QUERY_RFC1738);
        return preg_replace_callback('/%[0-9a-f]{2}/', function ($match) {
            return strtoupper($match[0]);
        }, $query);
    }
}
