<?php

namespace rainwaves\PayfastPayment\Client;

use rainwaves\PayfastPayment\Contract\PayFastSubscriptionInterface;
use rainwaves\PayfastPayment\Entities\SignatureTrait;
use rainwaves\PayfastPayment\Form\FormBuilder;
use rainwaves\PayfastPayment\Model\Sequence;
use rainwaves\PayfastPayment\Request\PayFastSubscriptionRequest;
use rainwaves\PayfastPayment\Validation\PayFastValidation;

class PayFastSubscriptionClient implements PayFastSubscriptionInterface
{
    use SignatureTrait;
    private \stdClass $config;

    private PayFastSubscriptionRequest $request;
    public function __construct(\stdClass $config)
    {
        $this->config = $config;
    }
    public function createForm(): string
    {
        $input = $this->request->toArray();

        $signature = $this->generateSignature($input, $this->config->pass_phrase);
        $input = Sequence::order($input);
        $input['signature'] = $signature;
        return FormBuilder::buildForm($input, $this->config->url);
    }
    public function createSubscriptionWithAForm(array $input): PayFastSubscriptionClient
    {
        PayFastValidation::validate($input);
        $input = (object)$input;
        $this->request = new PayFastSubscriptionRequest($input, $this->config);

        return $this;
    }

    public function getRequest(): PayFastSubscriptionRequest
    {
        return $this->request;
    }
}