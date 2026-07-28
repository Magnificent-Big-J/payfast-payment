<?php

namespace rainwaves\PayfastPayment\Contract;

use rainwaves\PayfastPayment\Http\HttpRequest;
use rainwaves\PayfastPayment\Http\HttpResponse;

interface HttpClientInterface
{
    public function send(HttpRequest $request): HttpResponse;
}

