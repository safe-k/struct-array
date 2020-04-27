<?php

namespace SK\StructArray\Property;

/**
 * @internal This class is used to represent a property that is missing
 * from the data structure.
 */
class Missing
{
    /** @var string */
    private $name;

    public static function property(string $name): self
    {
        $property = new static;
        $property->name = $name;
        return $property;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function __construct()
    {
    }
}
