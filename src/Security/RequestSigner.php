<?php

namespace rainwaves\PayfastPayment\Security;

final class RequestSigner
{
    public function __construct(private ?PayloadCanonicalizer $canonicalizer = null)
    {
        $this->canonicalizer ??= new PayloadCanonicalizer();
    }

    public function signApi(array $headers, array $body = [], array $query = [], ?string $passPhrase = null): string
    {
        return md5($this->canonicalizer->forApiSignature($headers, $body, $query, $passPhrase));
    }
}

