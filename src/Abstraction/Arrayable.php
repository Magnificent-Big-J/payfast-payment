<?php

namespace rainwaves\PayfastPayment\Abstraction;

abstract class Arrayable
{
    public function toArray(): array
    {
        $data = [];
        $reflection = new \ReflectionClass($this);

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName  = $property->getName();
            $propertyValue = $property->getValue($this);

            if (!$this->hasValue($propertyValue)) {
                continue;
            }

            $data[$this->snakeCase($propertyName)] = is_string($propertyValue)
                ? trim($propertyValue)
                : $propertyValue;
        }

        return $data;
    }

    private function snakeCase(string $value): string
    {
        $value = preg_replace('/(.)(?=[A-Z])/u', '$1_', $value);
        return strtolower($value);
    }

    private function hasValue($value): bool
    {
        if ($value === null) {
            return false;
        }
        if (is_string($value) && trim($value) === '') {
            return false;
        }
        return true;
    }
}
