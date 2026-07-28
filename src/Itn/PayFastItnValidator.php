<?php

namespace rainwaves\PayfastPayment\Itn;

use rainwaves\PayfastPayment\Exception\PayFastException;
use rainwaves\PayfastPayment\Response\PayFastResponse;
use rainwaves\PayfastPayment\Response\PayFastSubscriptionResponse;
use rainwaves\PayfastPayment\Support\PayFastSignatureHelper;

class PayFastItnValidator
{
    private array $data;
    private ?string $passPhrase;
    private ?string $rawBody;

    public function __construct(array $data, ?string $passPhrase = null, ?string $rawBody = null)
    {
        $this->data       = $data;
        $this->passPhrase = $passPhrase;
        $this->rawBody    = $rawBody;
    }

    public function validateSignature(): bool
    {
        if (is_string($this->rawBody) && $this->rawBody !== '') {
            $rawResult = $this->validateSignatureFromRawBody();
            if ($rawResult !== null) {
                return $rawResult;
            }
        }

        if (!isset($this->data['signature'])) {
            return false;
        }

        return hash_equals($this->generateItnSignature(), (string) $this->data['signature']);
    }

    public function validateAmount(string $expectedAmount): bool
    {
        if (!isset($this->data['amount_gross'])) {
            return false;
        }

        $expected = $this->normalizeAmount($expectedAmount);
        $actual   = $this->normalizeAmount((string) $this->data['amount_gross']);

        if ($expected === null || $actual === null) {
            return false;
        }

        return hash_equals($expected, $actual);
    }

    public function validateMerchantId(string $expectedMerchantId): bool
    {
        if (!isset($this->data['merchant_id'])) {
            return false;
        }

        return hash_equals((string) $expectedMerchantId, (string) $this->data['merchant_id']);
    }

    /**
     * Validate that the ITN request originated from a known PayFast IP range.
     *
     * Pass $_SERVER['REMOTE_ADDR'] (or equivalent) as $remoteIp.
     * Set $sandboxMode = true when using the PayFast sandbox environment.
     *
     * IMPORTANT: Always call this before trusting any other part of the ITN payload.
     */
    public function validateSourceIp(string $remoteIp, bool $sandboxMode = false): bool
    {
        return PayFastIpValidator::isValid($remoteIp, $sandboxMode);
    }

    /**
     * Confirm the ITN with PayFast's server-side validation endpoint.
     *
     * Uses cURL with full TLS certificate verification. Throws PayFastException on
     * network failure so callers can distinguish "cannot reach PayFast" from "INVALID".
     *
     * @throws PayFastException when the HTTP request itself fails.
     */
    public function validateWithPayFastEndpoint(string $validateUrl): bool
    {
        if (!function_exists('curl_init')) {
            throw PayFastException::curlNotAvailable();
        }

        $query = http_build_query($this->data, '', '&', PHP_QUERY_RFC1738);

        $ch = curl_init($validateUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $query,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT      => 'rainwaves/payfast-payment PHP/' . PHP_VERSION,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        ]);

        $result = curl_exec($ch);
        $error  = curl_error($ch);
        $errno  = curl_errno($ch);
        curl_close($ch);

        if ($result === false) {
            throw PayFastException::endpointUnreachable($validateUrl, sprintf('[%d] %s', $errno, $error));
        }

        return trim((string) $result) === 'VALID';
    }

    /**
     * Return the PayFast transaction ID (pf_payment_id).
     *
     * Persist and check this value to prevent replay attacks: if the same
     * pf_payment_id arrives more than once, reject the duplicate ITN.
     */
    public function getPaymentId(): ?string
    {
        return isset($this->data['pf_payment_id']) ? (string) $this->data['pf_payment_id'] : null;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->data['payment_status'] ?? null;
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

        return md5(PayFastSignatureHelper::buildQuery($fields));
    }

    private function validateSignatureFromRawBody(): ?bool
    {
        if (!isset($this->data['signature'])) {
            return null;
        }

        $rawBodyNoSig = preg_replace('/(^|&)signature=[^&]*/', '', $this->rawBody);
        $rawBodyNoSig = ltrim((string) $rawBodyNoSig, '&');

        // Parse so passphrase can be inserted at its correct alphabetical position.
        // PayFast sorts all fields (including passphrase) before signing, so we must
        // mirror that order rather than blindly appending.
        parse_str($rawBodyNoSig, $parsed);

        if ($this->passPhrase !== null && $this->passPhrase !== '') {
            $parsed['passphrase'] = $this->passPhrase;
        }

        ksort($parsed);

        $fields = [];
        foreach ($parsed as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $fields[$key] = trim((string) $value);
        }

        return hash_equals(md5(PayFastSignatureHelper::buildQuery($fields)), (string) $this->data['signature']);
    }

    private function normalizeAmount(string $amount): ?string
    {
        $amount = trim($amount);

        if (!preg_match('/^(0|[1-9]\d*)(\.\d{1,2})?$/', $amount)) {
            return null;
        }

        [$rands, $cents] = array_pad(explode('.', $amount, 2), 2, '');

        return $rands . '.' . str_pad($cents, 2, '0');
    }
}
