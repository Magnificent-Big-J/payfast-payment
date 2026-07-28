<?php

namespace rainwaves\PayfastPayment\Result;

final class PaymentResult extends AbstractPayFastResult
{
    public function paymentId(): ?string
    {
        return $this->data()['pf_payment_id'] ?? $this->data()['payment_id'] ?? null;
    }
}

