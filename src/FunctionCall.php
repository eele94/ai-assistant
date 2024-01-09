<?php

namespace Eele94\Assistant;

class FunctionCall
{
    public function __construct(public string $name, public string $description, public array $parameters)
    {
    }

    public function serialize(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'parameters' => $this->parameters,
        ];
    }
}
