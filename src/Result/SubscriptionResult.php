<?php

namespace rainwaves\PayfastPayment\Result;

final class SubscriptionResult extends AbstractPayFastResult
{
    public function token(): ?string
    {
        return $this->data()['token'] ?? null;
    }

    public function status(): ?string
    {
        return $this->data()['status'] ?? null;
    }
}

