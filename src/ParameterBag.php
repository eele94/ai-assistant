<?php

namespace Eele94\Assistant;

class ParameterBag
{
    public static function make(string $type, array $properties = [], array $required = []): static
    {
        return new static($type, $properties, $required);
    }
    public function __construct(public string $type, public array $properties = [], public array $required = [])
    {
    }

    public function properties(array $properties): static
    {
        $this->properties = $properties;

        return $this;
    }

    public function addProperty(Property $property): static
    {
        $this->properties[$property->key] = $property;
        return $this;
    }

    public function addRequired(string $required): static
    {
        $this->required[] = $required;
        return $this;
    }

    public function removeRequired(string $required): void
    {
        $this->required = array_filter($this->required, fn ($item) => $item !== $required);
    }

    public function serialize(): array
    {
        $properties = [];
        foreach ($this->properties as $property) {
            $properties[$property->key] = $property->serialize();
        }

        return [
            'type' => $this->type,
            'properties' => $properties,
        ];
    }
}
