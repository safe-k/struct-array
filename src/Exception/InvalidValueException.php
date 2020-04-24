<?php

namespace SK\StructArray\Exception;

class InvalidValueException extends \Exception
{
    public function __construct(string $property)
    {
        parent::__construct("Invalid value for property '{$property}'");
    }
}
