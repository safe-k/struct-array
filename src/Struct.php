<?php

namespace SK\StructArray;

class Struct
{
    /** @var string */
    private $name;
    /** @var callable|Struct[] */
    private $interface;
    /** @var bool */
    private $exhaustive;

    /**
     * Struct constructor.
     *
     * @param string $name
     * @param callable|Struct[] $interface
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

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return callable|Struct[]
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
