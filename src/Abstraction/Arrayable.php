<?php

namespace rainwaves\PayfastPayment\Abstraction;

abstract class Arrayable
{
    public function toArray(): array
    {
        $data = [];
        $reflection = new \ReflectionClass($this);

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            $propertyValue = $property->getValue($this);

            if ($propertyValue !== null && $propertyValue !== '') {
                $data[$this->snakeCase($propertyName)] = trim($propertyValue);
            }
        }

        return $data;
    }

    private function snakeCase(string $value): string
    {
        $value = preg_replace('/(.)(?=[A-Z])/u', '$1_', $value);
        return strtolower($value);
    }
}
