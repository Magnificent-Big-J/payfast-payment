<?php

namespace rainwaves\PayfastPayment\Client;

use rainwaves\PayfastPayment\Contract\PayFastInterface;
use rainwaves\PayfastPayment\Contract\HttpClientInterface;
use rainwaves\PayfastPayment\Entities\SignatureTrait;
use rainwaves\PayfastPayment\Exception\PayFastException;
use rainwaves\PayfastPayment\Form\FormBuilder;
use rainwaves\PayfastPayment\Model\Route;
use rainwaves\PayfastPayment\Model\Sequence;
use rainwaves\PayfastPayment\Request\PayFastRequest;
use rainwaves\PayfastPayment\Support\Environment;
use rainwaves\PayfastPayment\Validation\PayFastValidation;

class PayFastClient implements PayFastInterface
{
    use SignatureTrait;

    private \stdClass $config;
    private PayFastRequest $request;

    public function __construct(\stdClass $config)
    {
        $this->config = $config;
    }

    public static function make(array $config, ?HttpClientInterface $http = null): self
    {
        $environment = Environment::normalize((string) ($config['environment'] ?? $config['env'] ?? Environment::SANDBOX));
        $config['environment'] = $environment;
        $config['env'] = $environment;
        $config['url'] = Route::getUrl($environment);
        $config['http'] = $http;

        return new self((object) $config);
    }

    public function checkout(): self
    {
        return $this;
    }

    public function subscriptions(): SubscriptionClient
    {
        return new SubscriptionClient((array) $this->config, $this->config->http ?? null);
    }

    public function itn(): \rainwaves\PayfastPayment\Client\ItnClient
    {
        return new ItnClient((array) $this->config);
    }

    public function createForm(): string
    {
        if (!isset($this->request)) {
            throw PayFastException::notInitialized('makePaymentWithAForm');
        }

        $input = $this->request->toArray();
        $signature = $this->generateSignature($input, $this->config->pass_phrase);
        $input = Sequence::order($input);
        $input['signature'] = $signature;
        return FormBuilder::buildForm($input, $this->config->url);
    }

    public function makePaymentWithAForm(array $input): self
    {
        PayFastValidation::validate($input);
        $input = (object) $input;
        $this->request = new PayFastRequest($input, $this->config);

        return $this;
    }

    public function getRequest(): PayFastRequest
    {
        return $this->request;
    }
}
