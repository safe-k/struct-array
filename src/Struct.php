<?php

namespace SK\StructArray;

class Struct
{
    /** @var string */
    private $name;
    /** @var callable[]|Struct[] */
    private $interface;
    /** @var bool */
    private $exhaustive;

    /**
     * Struct constructor.
     *
     * @param string $name Used in error messaging, useful when nesting or validating multiple Structs.
     * @param callable[]|Struct[] $interface A list of expected keys and their associated validator.
     * @param bool $exhaustive Used to specify whether the declared Struct properties are exhaustive,
     * meaning data arrays submitted for validation must not contain unknown keys. This defaults
     * to `true`; Set to `false` if you only want to validate some of the array elements.
     * @return Struct
     */
    public static function of(
        string $name,
        array $interface,
        bool $exhaustive = true
    ): self {
        $struct = new static;
        $struct->name = $name;
        $struct->interface = $interface;
        $struct->exhaustive = $exhaustive;
        return $struct;
    }

    /**
     * @internal
     *
     * @param array $interface
     * @return static
     */
    public static function default(array $interface): self
    {
        return static::of('Struct', $interface, false);
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return callable[]|Struct[]
     */
    public function interface(): array
    {
        return $this->interface;
    }

    public function isExhaustive(): bool
    {
        return $this->exhaustive;
    }

    public function __construct()
    {
    }
}
