<?php

namespace SK\StructArray\Property;

/**
 * Class Missing
 *
 * This class is used to represent a property that is missing from the
 * data structure.
 *
 * @package SK\StructArray\Property
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
