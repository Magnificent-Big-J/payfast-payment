<?php

namespace rainwaves\PayfastPayment\Entities;

use rainwaves\PayfastPayment\Model\Sequence;

trait SignatureTrait
{
    protected function generateSignature(array $data, $passPhrase = null): string
    {
        $fields = array();
        $data['passphrase'] = $passPhrase;

        foreach (Sequence::$sequenceOrder as $key) {
            if (!empty($data[$key])) {
                $fields[$key] = $data[$key];
            }
        }

        return md5(http_build_query($fields));
    }
}