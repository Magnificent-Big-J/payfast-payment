<?php

namespace rainwaves\PayfastPayment\Http;

use rainwaves\PayfastPayment\Contract\HttpClientInterface;
use rainwaves\PayfastPayment\Exception\PayFastException;
use rainwaves\PayfastPayment\Exception\TimeoutException;
use rainwaves\PayfastPayment\Exception\TransportException;

final class CurlHttpClient implements HttpClientInterface
{
    private const MAX_RESPONSE_BYTES = 1048576;

    public function send(HttpRequest $request): HttpResponse
    {
        if (!function_exists('curl_init')) {
            throw PayFastException::curlNotAvailable();
        }

        $headers = [];
        foreach ($request->headers() as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }

        $responseHeaders = [];
        $body = '';
        $ch = curl_init($request->url());
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => $request->method(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_CONNECTTIMEOUT => $request->connectTimeoutSeconds(),
            CURLOPT_TIMEOUT        => $request->timeoutSeconds(),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_USERAGENT      => 'rainwaves/payfast-payment PHP/' . PHP_VERSION,
            CURLOPT_HTTPHEADER     => array_merge($headers, ['Content-Type: application/x-www-form-urlencoded']),
            CURLOPT_HEADERFUNCTION => static function ($curl, string $header) use (&$responseHeaders): int {
                $length = strlen($header);
                $parts = explode(':', $header, 2);
                if (count($parts) === 2) {
                    $responseHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
                }
                return $length;
            },
            CURLOPT_WRITEFUNCTION => static function ($curl, string $chunk) use (&$body): int {
                $body = ($body ?? '') . $chunk;
                if (strlen($body) > self::MAX_RESPONSE_BYTES) {
                    return 0;
                }
                return strlen($chunk);
            },
        ]);

        if ($request->method() !== 'GET' && $request->body() !== []) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request->encodedBody());
        }

        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($result === false) {
            if (in_array($errno, [CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED], true)) {
                throw new TimeoutException('PayFast API request timed out: ' . $error);
            }

            throw new TransportException('PayFast API transport failure: ' . $error);
        }

        return new HttpResponse($status, $responseHeaders, $body);
    }
}
