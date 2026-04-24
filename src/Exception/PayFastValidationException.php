<?php

namespace rainwaves\PayfastPayment\Exception;

class PayFastValidationException extends \InvalidArgumentException
{
    /** @var string[] */
    private array $validationErrors;

    /**
     * @param string[] $errors
     */
    public function __construct(array $errors)
    {
        $this->validationErrors = array_values($errors);
        parent::__construct('Invalid input data: ' . implode(' ', $errors));
    }

    /**
     * @param string[] $errors
     */
    public static function withErrors(array $errors): self
    {
        return new self($errors);
    }

    /**
     * @return string[]
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
