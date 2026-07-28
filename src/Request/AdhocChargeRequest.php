<?php

namespace rainwaves\PayfastPayment\Request;

use InvalidArgumentException;
use rainwaves\PayfastPayment\Support\Money;

final class AdhocChargeRequest
{
    public function __construct(
        private Money $amount,
        private string $itemName,
        private ?string $mPaymentId = null,
        private ?string $itemDescription = null,
        private ?bool $sendItn = null,
        private ?string $setup = null
    ) {
        if (trim($itemName) === '') {
            throw new InvalidArgumentException('Ad hoc charge item name is required.');
        }
    }

    public function toArray(): array
    {
        return array_filter([
            'amount' => $this->amount->toCents(),
            'item_name' => $this->itemName,
            'm_payment_id' => $this->mPaymentId,
            'item_description' => $this->itemDescription,
            'itn' => $this->sendItn === null ? null : ($this->sendItn ? 1 : 0),
            'setup' => $this->setup,
        ], static fn ($value): bool => $value !== null && $value !== '');
    }
}

