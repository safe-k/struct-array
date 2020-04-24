<?php

namespace SK\StructArray\Exception;

class InvalidValidatorException extends \Exception
{
    public function __construct($invalidValidator)
    {
        $type = gettype($invalidValidator);
        parent::__construct("Cannot use '{$type}' as struct value validator");
    }
}
