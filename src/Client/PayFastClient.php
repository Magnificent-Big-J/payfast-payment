<?php

namespace rainwaves\PayfastPayment\Client;

use rainwaves\PayfastPayment\Contract\PayFastInterface;
use rainwaves\PayfastPayment\Entities\SignatureTrait;
use rainwaves\PayfastPayment\Form\FormBuilder;
use rainwaves\PayfastPayment\Model\Sequence;
use rainwaves\PayfastPayment\Request\PayFastRequest;
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

    public function createForm(): string
    {
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
