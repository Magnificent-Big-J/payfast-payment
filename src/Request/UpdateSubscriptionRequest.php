<?php

namespace rainwaves\PayfastPayment\Request;

use InvalidArgumentException;
use rainwaves\PayfastPayment\Model\Frequency;
use rainwaves\PayfastPayment\Support\Money;

final class UpdateSubscriptionRequest
{
    private array $fields;

    private function __construct(array $fields)
    {
        if ($fields === []) {
            throw new InvalidArgumentException('At least one subscription update field is required.');
        }

        $this->fields = $fields;
    }

    public static function make(
        ?int $cycles = null,
        ?int $frequency = null,
        ?string $runDate = null,
        ?Money $amount = null
    ): self {
        $fields = [];

        if ($cycles !== null) {
            if ($cycles < 0) {
                throw new InvalidArgumentException('Subscription cycles cannot be negative.');
            }
            $fields['cycles'] = $cycles;
        }

        if ($frequency !== null) {
            if (!Frequency::isValid($frequency)) {
                throw new InvalidArgumentException('Invalid PayFast subscription frequency.');
            }
            $fields['frequency'] = $frequency;
        }

        if ($runDate !== null) {
            $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $runDate);
            if (!$date || $date->format('Y-m-d') !== $runDate) {
                throw new InvalidArgumentException('Subscription run_date must use Y-m-d format.');
            }
            $fields['run_date'] = $runDate;
        }

        if ($amount !== null) {
            $fields['amount'] = $amount->toCents();
        }

        return new self($fields);
    }

    public function toArray(): array
    {
        return $this->fields;
    }
}

