<?php

namespace rainwaves\PayfastPayment\Result;

final class CardUpdateLinkResult extends AbstractPayFastResult
{
    public function url(): ?string
    {
        return $this->data()['url'] ?? null;
    }
}

