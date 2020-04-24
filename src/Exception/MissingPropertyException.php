<?php

namespace SK\StructArray\Exception;

class MissingPropertyException extends \Exception
{
    public function __construct(string $property)
    {
        parent::__construct("missing value for '{$property}'");
    }
}
