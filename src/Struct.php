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
     * @param bool $exhaustive
     * @return Struct
     */
    public static function of(string $name, array $interface, bool $exhaustive = true): self
    {
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
