<?php

namespace SK\StructArray;

use SK\StructArray\Property\Missing;

class Validator
{
    /**
     * @param array $data
     * @param array|Struct $struct If an array is supplied, a generic, non-exhaustive Struct is used.
     * @return bool
     * @throws Exception\StructValidationException
     */
    public static function validate(array &$data, $struct): bool
    {
        try {
            if (is_array($struct)) {
                $struct = Struct::default($struct);
            }

            $unexpectedProperties = array_diff_key($data, $struct->interface());
            if ($struct->isExhaustive() && !empty($unexpectedProperties)) {
                throw new Exception\UnexpectedPropertyException(...array_keys($unexpectedProperties));
            }

            foreach ($struct->interface() as $field => $validator) {
                $value = array_key_exists($field, $data)
                    ? $data[$field]
                    : Missing::property($field);
                $propertyIsMissing = is_a($value, Missing::class);

                if (is_callable($validator)) {
                    if (!$validator($value)) {
                        throw new Exception\InvalidValueException($field);
                    }
                    // If value has changed, set corresponding data field
                    if ($propertyIsMissing && !is_a($value, Missing::class)) {
                        $data[$field] = $value;
                    }
                    return true;
                }

                if ($propertyIsMissing) {
                    throw new Exception\MissingPropertyException($field);
                }

                if (is_array($validator)) {
                    $validator = Struct::default($validator);
                }
                if (is_a($validator, Struct::class)) {
                    if (!validate($value, $validator)) {
                        throw new Exception\InvalidValueException($field);
                    }
                    return true;
                }

                throw new Exception\InvalidValidatorException($validator);
            }
        } catch (\Throwable $t) {
            /**
             * Wrap throwables with custom validation exception.
             *
             * This is only done once to avoid double wrapping, in case the throw
             * occurred during a recursive call.
             */
            if (!is_a($t, Exception\StructValidationException::class)) {
                $t = new Exception\StructValidationException($struct, $t);
            }

            throw $t;
        }

        return true;
    }
}
