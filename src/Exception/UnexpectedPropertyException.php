<?php

namespace SK\StructArray\Exception;

class UnexpectedPropertyException extends \Exception
{
    public function __construct(string ...$properties)
    {
        parent::__construct(sprintf(
            "Unexpected %s '%s'",
            count($properties) === 1 ? 'property' : 'properties',
            implode(', ', $properties)
        ));
    }
}
