<?php

namespace rainwaves\PayfastPayment\Client;

use rainwaves\PayfastPayment\Contract\ItnValidatorInterface;
use rainwaves\PayfastPayment\Itn\PayFastItnValidator;
use rainwaves\PayfastPayment\Request\ExpectedPayment;
use rainwaves\PayfastPayment\Result\ItnValidationResult;
use rainwaves\PayfastPayment\Security\Redactor;
use rainwaves\PayfastPayment\Support\Environment;
use rainwaves\PayfastPayment\Support\RouteResolver;

final class ItnClient implements ItnValidatorInterface
{
    private string $environment;
    private string $passPhrase;
    private RouteResolver $routes;
    private Redactor $redactor;

    public function __construct(array $config)
    {
        $this->environment = Environment::normalize((string) ($config['environment'] ?? $config['env'] ?? Environment::SANDBOX));
        $this->passPhrase = (string) ($config['pass_phrase'] ?? '');
        $this->routes = new RouteResolver();
        $this->redactor = new Redactor();
    }

    public function validate(
        array $payload,
        ?string $rawBody,
        string $remoteIp,
        ExpectedPayment $expected,
        bool $confirmWithPayFast = true
    ): ItnValidationResult {
        $validator = new PayFastItnValidator($payload, $this->passPhrase, $rawBody);
        $checks = [
            'source_ip' => $validator->validateSourceIp($remoteIp, Environment::isSandbox($this->environment)),
            'signature' => $validator->validateSignature(),
            'amount' => $validator->validateAmount($expected->amount->toDecimal()),
            'merchant_id' => $validator->validateMerchantId($expected->merchantId),
        ];

        if ($confirmWithPayFast) {
            try {
                $checks['server_confirmation'] = $validator->validateWithPayFastEndpoint(
                    $this->routes->validationUrl($this->environment)
                );
            } catch (\Throwable $e) {
                $checks['server_confirmation'] = false;
            }
        }

        return new ItnValidationResult(
            $checks,
            $validator->getPaymentId(),
            $validator->getPaymentStatus(),
            $this->redactor->redact($payload)
        );
    }
}

