<?php

namespace rainwaves\PayfastPayment\Contract;

use rainwaves\PayfastPayment\Request\AdhocChargeRequest;
use rainwaves\PayfastPayment\Request\CardUpdateLinkRequest;
use rainwaves\PayfastPayment\Request\PauseSubscriptionRequest;
use rainwaves\PayfastPayment\Request\UpdateSubscriptionRequest;
use rainwaves\PayfastPayment\Result\CardUpdateLinkResult;
use rainwaves\PayfastPayment\Result\PaymentResult;
use rainwaves\PayfastPayment\Result\SubscriptionResult;

interface SubscriptionClientInterface
{
    public function fetch(string $token): SubscriptionResult;

    public function pause(string $token, PauseSubscriptionRequest $request): SubscriptionResult;

    public function unpause(string $token): SubscriptionResult;

    public function cancel(string $token): SubscriptionResult;

    public function update(string $token, UpdateSubscriptionRequest $request): SubscriptionResult;

    public function adhoc(string $token, AdhocChargeRequest $request): PaymentResult;

    public function cardUpdateLink(string $token, CardUpdateLinkRequest $request): CardUpdateLinkResult;
}

