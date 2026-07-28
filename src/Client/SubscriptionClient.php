<?php

namespace rainwaves\PayfastPayment\Client;

use rainwaves\PayfastPayment\Contract\ClockInterface;
use rainwaves\PayfastPayment\Contract\HttpClientInterface;
use rainwaves\PayfastPayment\Contract\SubscriptionClientInterface;
use rainwaves\PayfastPayment\Exception\ConfigurationException;
use rainwaves\PayfastPayment\Http\CurlHttpClient;
use rainwaves\PayfastPayment\Http\HttpRequest;
use rainwaves\PayfastPayment\Http\ResponseDecoder;
use rainwaves\PayfastPayment\Request\AdhocChargeRequest;
use rainwaves\PayfastPayment\Request\CardUpdateLinkRequest;
use rainwaves\PayfastPayment\Request\PauseSubscriptionRequest;
use rainwaves\PayfastPayment\Request\UpdateSubscriptionRequest;
use rainwaves\PayfastPayment\Result\CardUpdateLinkResult;
use rainwaves\PayfastPayment\Result\PaymentResult;
use rainwaves\PayfastPayment\Result\SubscriptionResult;
use rainwaves\PayfastPayment\Security\RequestSigner;
use rainwaves\PayfastPayment\Support\Environment;
use rainwaves\PayfastPayment\Support\RouteResolver;
use rainwaves\PayfastPayment\Support\SystemClock;

final class SubscriptionClient implements SubscriptionClientInterface
{
    private string $merchantId;
    private string $passPhrase;
    private string $environment;
    private RouteResolver $routes;
    private RequestSigner $signer;
    private ResponseDecoder $decoder;

    public function __construct(
        array $config,
        private ?HttpClientInterface $http = null,
        private ?ClockInterface $clock = null
    ) {
        $this->merchantId = (string) ($config['merchant_id'] ?? '');
        $this->passPhrase = (string) ($config['pass_phrase'] ?? '');
        $this->environment = Environment::normalize((string) ($config['environment'] ?? $config['env'] ?? Environment::SANDBOX));
        $this->assertConfigured();
        $this->http ??= new CurlHttpClient();
        $this->clock ??= new SystemClock();
        $this->routes = new RouteResolver();
        $this->signer = new RequestSigner();
        $this->decoder = new ResponseDecoder();
    }

    public function fetch(string $token): SubscriptionResult
    {
        return $this->sendSubscription('GET', $token, 'fetch', []);
    }

    public function pause(string $token, PauseSubscriptionRequest $request): SubscriptionResult
    {
        return $this->sendSubscription('PUT', $token, 'pause', $request->toArray());
    }

    public function unpause(string $token): SubscriptionResult
    {
        return $this->sendSubscription('PUT', $token, 'unpause', []);
    }

    public function cancel(string $token): SubscriptionResult
    {
        return $this->sendSubscription('PUT', $token, 'cancel', []);
    }

    public function update(string $token, UpdateSubscriptionRequest $request): SubscriptionResult
    {
        return $this->sendSubscription('PATCH', $token, 'update', $request->toArray());
    }

    public function adhoc(string $token, AdhocChargeRequest $request): PaymentResult
    {
        $response = $this->send('POST', $token, 'adhoc', $request->toArray());

        return new PaymentResult($response['statusCode'], $response['payload']);
    }

    public function cardUpdateLink(string $token, CardUpdateLinkRequest $request): CardUpdateLinkResult
    {
        $this->assertToken($token);
        $url = $this->routes->recurringCardUpdateUrl($token, $request->returnUrl(), $this->environment);

        return new CardUpdateLinkResult(200, [
            'code' => 200,
            'status' => 'success',
            'data' => ['url' => $url],
        ]);
    }

    private function sendSubscription(string $method, string $token, string $operation, array $body): SubscriptionResult
    {
        $response = $this->send($method, $token, $operation, $body);

        return new SubscriptionResult($response['statusCode'], $response['payload']);
    }

    private function send(string $method, string $token, string $operation, array $body): array
    {
        $this->assertToken($token);
        $url = $this->routes->subscriptionUrl($token, $operation, $this->environment);
        $query = Environment::isSandbox($this->environment) ? ['testing' => 'true'] : [];
        $headers = $this->signedHeaders($body, $query);

        $httpResponse = $this->http->send(new HttpRequest($method, $url, $headers, $body));

        return [
            'statusCode' => $httpResponse->statusCode(),
            'payload' => $this->decoder->decode($httpResponse),
        ];
    }

    private function signedHeaders(array $body, array $query): array
    {
        $headers = [
            'merchant-id' => $this->merchantId,
            'version' => 'v1',
            'timestamp' => $this->clock->now()->format('Y-m-d\TH:i:sP'),
        ];

        $headers['signature'] = $this->signer->signApi($headers, $body, $query, $this->passPhrase);

        return $headers;
    }

    private function assertToken(string $token): void
    {
        if (trim($token) === '') {
            throw new \InvalidArgumentException('PayFast subscription token is required.');
        }
    }

    private function assertConfigured(): void
    {
        if (trim($this->merchantId) === '') {
            throw new ConfigurationException('PayFast merchant_id is required for subscription API calls.');
        }

        if (trim($this->passPhrase) === '') {
            throw new ConfigurationException('PayFast pass_phrase is required for subscription API calls.');
        }
    }
}
