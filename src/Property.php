<?php

namespace Eele94\Assistant;

class Property
{
    /**
     * todo: logic for type: array
     * todo: logic for type: object
     */
    protected $enum = null;

    protected $items = null;

    public static function make(?string $key = null, ?string $description = null, mixed $type = null): static
    {
        return new static($key, $description, $type);
    }

    public function __construct(public ?string $key, public ?string $description, public mixed $type)
    {
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function setArray(Property $property)
    {
        $this->type = 'array';
        $this->items = $property;

        return $this;
    }

    public function setType(string|array $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function addEnum(array $enum): static
    {
        $this->enum = $enum;

        return $this;
    }

    public function serialize(): array
    {
        $result = array_filter([
            'type' => $this->type,
            'description' => $this->description,
            'enum' => $this->enum,
            'items' => $this->items?->serialize(),
        ]);

        return $result;
    }
}
