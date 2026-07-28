<?php

namespace rainwaves\PayfastPayment\Request;

final class CardUpdateLinkRequest
{
    public function __construct(private ?string $returnUrl = null)
    {
    }

    public function returnUrl(): ?string
    {
        return $this->returnUrl;
    }
}

