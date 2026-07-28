<?php

namespace rainwaves\PayfastPayment\Tests\Unit;

use PHPUnit\Framework\TestCase;
use rainwaves\PayfastPayment\Client\SubscriptionClient;
use rainwaves\PayfastPayment\Contract\ClockInterface;
use rainwaves\PayfastPayment\Contract\HttpClientInterface;
use rainwaves\PayfastPayment\Http\HttpRequest;
use rainwaves\PayfastPayment\Http\HttpResponse;
use rainwaves\PayfastPayment\Http\ResponseDecoder;
use rainwaves\PayfastPayment\Exception\InvalidResponseException;
use rainwaves\PayfastPayment\Exception\ConfigurationException;
use rainwaves\PayfastPayment\Request\AdhocChargeRequest;
use rainwaves\PayfastPayment\Request\CardUpdateLinkRequest;
use rainwaves\PayfastPayment\Request\PauseSubscriptionRequest;
use rainwaves\PayfastPayment\Request\UpdateSubscriptionRequest;
use rainwaves\PayfastPayment\Support\Money;

class SubscriptionClientTest extends TestCase
{
    public function testPauseBuildsSignedOfflineHttpRequest(): void
    {
        $http = new CapturingHttpClient();
        $client = new SubscriptionClient($this->config(), $http, new FixedClock());

        $result = $client->pause('2afa4575-5628-051a-d0ed-4e071b56a7b0', new PauseSubscriptionRequest(1));

        $this->assertTrue($result->successful());
        $this->assertSame('PUT', $http->request->method());
        $this->assertSame(
            'https://api.payfast.co.za/subscriptions/2afa4575-5628-051a-d0ed-4e071b56a7b0/pause?testing=true',
            $http->request->url()
        );
        $this->assertSame(['cycles' => 1], $http->request->body());
        $this->assertSame('10000100', $http->request->headers()['merchant-id']);
        $this->assertSame('v1', $http->request->headers()['version']);
        $this->assertSame('2020-03-23T09:46:06+02:00', $http->request->headers()['timestamp']);
        $this->assertSame('d46a6a5825d2d176031de7ae030332f8', $http->request->headers()['signature']);
    }

    public function testFetchBuildsOfflineHttpRequest(): void
    {
        $http = new CapturingHttpClient();
        $client = new SubscriptionClient($this->config(), $http, new FixedClock());

        $client->fetch('2afa4575-5628-051a-d0ed-4e071b56a7b0');

        $this->assertSame('GET', $http->request->method());
        $this->assertSame(
            'https://api.payfast.co.za/subscriptions/2afa4575-5628-051a-d0ed-4e071b56a7b0/fetch?testing=true',
            $http->request->url()
        );
        $this->assertSame([], $http->request->body());
        $this->assertNotEmpty($http->request->headers()['signature']);
    }

    public function testProductionRequestsDoNotIncludeTestingQueryParameter(): void
    {
        $http = new CapturingHttpClient();
        $client = new SubscriptionClient(array_merge($this->config(), [
            'environment' => 'production',
        ]), $http, new FixedClock());

        $client->fetch('2afa4575-5628-051a-d0ed-4e071b56a7b0');

        $this->assertSame(
            'https://api.payfast.co.za/subscriptions/2afa4575-5628-051a-d0ed-4e071b56a7b0/fetch',
            $http->request->url()
        );
    }

    public function testProviderFailureResponseIsNormalizedAsUnsuccessfulResult(): void
    {
        $http = new CapturingHttpClient(new HttpResponse(401, ['content-type' => 'application/json'], json_encode([
            'code' => 401,
            'status' => 'failed',
            'data' => [
                'response' => false,
                'message' => 'Merchant authorisation failed',
            ],
        ])));
        $client = new SubscriptionClient($this->config(), $http, new FixedClock());

        $result = $client->fetch('2afa4575-5628-051a-d0ed-4e071b56a7b0');

        $this->assertFalse($result->successful());
        $this->assertSame(401, $result->statusCode());
        $this->assertSame('401', $result->providerCode());
        $this->assertSame('Merchant authorisation failed', $result->providerMessage());
    }

    public function testUnpauseBuildsOfflineHttpRequest(): void
    {
        $http = new CapturingHttpClient();
        $client = new SubscriptionClient($this->config(), $http, new FixedClock());

        $client->unpause('2afa4575-5628-051a-d0ed-4e071b56a7b0');

        $this->assertSame('PUT', $http->request->method());
        $this->assertSame(
            'https://api.payfast.co.za/subscriptions/2afa4575-5628-051a-d0ed-4e071b56a7b0/unpause?testing=true',
            $http->request->url()
        );
        $this->assertSame([], $http->request->body());
    }

    public function testCancelBuildsOfflineHttpRequest(): void
    {
        $http = new CapturingHttpClient();
        $client = new SubscriptionClient($this->config(), $http, new FixedClock());

        $client->cancel('2afa4575-5628-051a-d0ed-4e071b56a7b0');

        $this->assertSame('PUT', $http->request->method());
        $this->assertSame(
            'https://api.payfast.co.za/subscriptions/2afa4575-5628-051a-d0ed-4e071b56a7b0/cancel?testing=true',
            $http->request->url()
        );
        $this->assertSame([], $http->request->body());
    }

    public function testUpdateBuildsOfflineHttpRequest(): void
    {
        $http = new CapturingHttpClient();
        $client = new SubscriptionClient($this->config(), $http, new FixedClock());

        $client->update(
            '2afa4575-5628-051a-d0ed-4e071b56a7b0',
            UpdateSubscriptionRequest::make(cycles: 0, frequency: 3, runDate: '2026-08-01', amount: Money::zar('25.00'))
        );

        $this->assertSame('PATCH', $http->request->method());
        $this->assertSame(
            'https://api.payfast.co.za/subscriptions/2afa4575-5628-051a-d0ed-4e071b56a7b0/update?testing=true',
            $http->request->url()
        );
        $this->assertSame([
            'cycles' => 0,
            'frequency' => 3,
            'run_date' => '2026-08-01',
            'amount' => 2500,
        ], $http->request->body());
    }

    public function testAdhocBuildsOfflineHttpRequest(): void
    {
        $http = new CapturingHttpClient();
        $client = new SubscriptionClient($this->config(), $http, new FixedClock());

        $client->adhoc(
            '2afa4575-5628-051a-d0ed-4e071b56a7b0',
            new AdhocChargeRequest(Money::zar('25.00'), 'Plan top-up', 'INV-001')
        );

        $this->assertSame('POST', $http->request->method());
        $this->assertSame(
            'https://api.payfast.co.za/subscriptions/2afa4575-5628-051a-d0ed-4e071b56a7b0/adhoc?testing=true',
            $http->request->url()
        );
        $this->assertSame([
            'amount' => 2500,
            'item_name' => 'Plan top-up',
            'm_payment_id' => 'INV-001',
        ], $http->request->body());
    }

    public function testUpdateUsesDocumentedFieldsOnly(): void
    {
        $request = UpdateSubscriptionRequest::make(
            cycles: 0,
            frequency: 3,
            runDate: '2026-08-01',
            amount: Money::zar('25.00')
        );

        $this->assertSame([
            'cycles' => 0,
            'frequency' => 3,
            'run_date' => '2026-08-01',
            'amount' => 2500,
        ], $request->toArray());
    }

    public function testAdhocBuildsCentAmountPayload(): void
    {
        $request = new AdhocChargeRequest(Money::zar('25.00'), 'Plan top-up', 'INV-001');

        $this->assertSame([
            'amount' => 2500,
            'item_name' => 'Plan top-up',
            'm_payment_id' => 'INV-001',
        ], $request->toArray());
    }

    public function testCardUpdateLinkIsGeneratedWithoutNetworkRequest(): void
    {
        $http = new CapturingHttpClient();
        $client = new SubscriptionClient($this->config(), $http, new FixedClock());

        $result = $client->cardUpdateLink(
            '2afa4575-5628-051a-d0ed-4e071b56a7b0',
            new CardUpdateLinkRequest('https://example.com/billing')
        );

        $this->assertTrue($result->successful());
        $this->assertSame(
            'https://sandbox.payfast.co.za/eng/recurring/update/2afa4575-5628-051a-d0ed-4e071b56a7b0?return=https%3A%2F%2Fexample.com%2Fbilling',
            $result->url()
        );
        $this->assertNull($http->request);
    }

    public function testResponseDecoderRejectsMalformedJson(): void
    {
        $this->expectException(InvalidResponseException::class);

        (new ResponseDecoder())->decode(new HttpResponse(200, [], '{bad json'));
    }

    public function testResponseDecoderRejectsUnsupportedContentType(): void
    {
        $this->expectException(InvalidResponseException::class);

        (new ResponseDecoder())->decode(new HttpResponse(200, ['Content-Type' => 'text/html'], '<html></html>'));
    }

    public function testSubscriptionClientRequiresMerchantId(): void
    {
        $this->expectException(ConfigurationException::class);

        new SubscriptionClient([
            'pass_phrase' => 'secret',
            'environment' => 'sandbox',
        ], new CapturingHttpClient(), new FixedClock());
    }

    public function testSubscriptionClientRequiresPassphrase(): void
    {
        $this->expectException(ConfigurationException::class);

        new SubscriptionClient([
            'merchant_id' => '10000100',
            'environment' => 'sandbox',
        ], new CapturingHttpClient(), new FixedClock());
    }

    private function config(): array
    {
        return [
            'merchant_id' => '10000100',
            'pass_phrase' => 'secret',
            'environment' => 'sandbox',
        ];
    }
}

final class CapturingHttpClient implements HttpClientInterface
{
    public ?HttpRequest $request = null;

    public function __construct(private ?HttpResponse $response = null)
    {
    }

    public function send(HttpRequest $request): HttpResponse
    {
        $this->request = $request;

        return $this->response ?? new HttpResponse(200, ['content-type' => 'application/json'], json_encode([
            'code' => 200,
            'status' => 'success',
            'data' => ['response' => true],
        ]));
    }
}

final class FixedClock implements ClockInterface
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2020-03-23T09:46:06+02:00');
    }
}
