<?php

namespace rainwaves\PayfastPayment\Http;

use rainwaves\PayfastPayment\Exception\InvalidResponseException;

final class ResponseDecoder
{
    public function decode(HttpResponse $response): array
    {
        $body = trim($response->body());

        if ($body === '') {
            return [
                'code' => $response->statusCode(),
                'status' => $response->statusCode() >= 200 && $response->statusCode() < 300 ? 'success' : 'failed',
                'data' => null,
            ];
        }

        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidResponseException('PayFast API returned malformed JSON: ' . json_last_error_msg());
        }

        if (!is_array($decoded)) {
            throw new InvalidResponseException('PayFast API returned an unsupported JSON response.');
        }

        return $decoded;
    }
}

