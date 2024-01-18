<?php

namespace Eele94\Assistant;

class FunctionCall
{
    public function __construct(public string $name, public string $description, public ParameterBag $parameters)
    {
    }

    public static function make(string $name, string $description, ParameterBag $parameters): static
    {
        return new static($name, $description, $parameters);
    }

    public function serialize(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'parameters' => $this->parameters->serialize(),
        ];
    }
}
