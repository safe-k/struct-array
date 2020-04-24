<?php

namespace SK\StructArray\Exception;

use SK\StructArray\Struct;

class StructValidationException extends \Exception
{
    public function __construct(Struct $struct, \Exception $reason)
    {
        parent::__construct(
            "Struct '{$struct->name()}' failed validation: {$reason->getMessage()}",
            0,
            $reason
        );
    }
}
