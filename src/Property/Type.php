<?php

namespace SK\StructArray\Property;

use SK\StructArray\Exception;
use SK\StructArray\Struct;

use function SK\StructArray\validate;

/**
 * Contains static methods that return validator functions; Each said function
 * takes in a $value parameter, validates it, and returns a boolean value.
 */
class Type
{
    /**
     * Accepts 1+ callable $validators and returns `true` if the $value successfully
     * passes through all of them.
     *
     * @example Type::allOf('is_int', 'is_numeric')
     *
     * @param callable ...$validators
     * @return callable
     */
    public static function allOf(callable ...$validators): callable
    {
        return function ($value) use ($validators): bool {
            foreach ($validators as $validator) {
                if (!$validator($value)) {
                    return false;
                }
            }

            return true;
        };
    }

    /**
     * Accepts 1+ callable $validators and/or instances of \SK\StructArray\Struct,
     * and returns `true` if the $value successfully passes through any of them.
     *
     * @example Type::anyOf('is_int', 'is_float', 'is_null')
     *
     * @param callable|Struct ...$validators
     * @return callable
     */
    public static function anyOf(...$validators): callable
    {
        return function ($value) use ($validators): bool {
            foreach ($validators as $validator) {
                if (is_callable($validator)) {
                    if ($validator($value)) {
                        return true;
                    }
                } elseif (is_a($validator, Struct::class)) {
                    if (validate($value, $validator)) {
                        return true;
                    }
                } else {
                    throw new Exception\InvalidValidatorException();
                }
            }

            return false;
        };
    }

    /**
     * Accepts a callable $validator or instance of \SK\StructArray\Struct,
     * and returns `true` if the $value is an array and has successfully passed
     * through the $validator, or Struct validation.
     *
     * @example Type::arrayOf('is_string')
     *
     * @param callable|Struct $validator
     * @return callable
     */
    public static function arrayOf($validator): callable
    {
        return function ($value) use ($validator): bool {
            if (!is_array($value)) {
                return false;
            }

            if (is_callable($validator)) {
                foreach ($value as $element) {
                    if (!$validator($element)) {
                        return false;
                    }
                }
            } elseif (is_a($validator, Struct::class)) {
                foreach ($value as $element) {
                    if (!validate($element, $validator)) {
                        return false;
                    }
                }
            } else {
                throw new Exception\InvalidValidatorException();
            }

            return true;
        };
    }

    /**
     * Returns `true` if the $value is an instance of a class, with name matching $type.
     *
     * @example Type::classOf(\DateTime::class)
     *
     * @param string $type
     * @return callable
     */
    public static function classOf(string $type): callable
    {
        return function ($value) use ($type): bool {
            return is_a($value, $type);
        };
    }

    /**
     * Accepts a callable $validator and passes the $value to a negated version of it (ie.
     * returns `true` if the $validator returns `false`).
     *
     * @example Type::not('is_numeric')
     *
     * @param callable $validator
     * @return callable
     */
    public static function not(callable $validator): callable
    {
        return function ($value) use ($validator): bool {
            return !$validator($value);
        };
    }

    /**
     * Accepts a callable $validator and an optional $default value;
     * - If the $value is an instance of \SK\StructArray\Property\Missing, and no $default
     * was passed in, `true` is returned and the $validator is skipped.
     * - If the $value is an instance of \SK\StructArray\Property\Missing, but a $default
     * was passed in, $value is set to $default, and is then passed to the $validator,
     * returning `true` if it successfully passes.
     *
     * @example Type::optional('is_string', 'hello')
     *
     * @param callable $validator
     * @param string $default
     * @return callable
     */
    public static function optional(callable $validator, $default = Missing::class): callable
    {
        return function (&$value) use ($validator, $default): bool {
            if (is_a($value, Missing::class)) {
                if ($default === Missing::class) {
                    return true;
                }
                $value = $default;
            }

            return $validator($value);
        };
    }
}
