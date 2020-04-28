<?php

namespace SK\StructArray;

use SK\StructArray\Property\Missing;
use SK\StructArray\Property\Type;

/**
 * @see Type::allOf()
 *
 * @param callable[] $validators
 * @return callable
 */
function allOf(callable ...$validators): callable
{
    return Type::allOf(...$validators);
}

/**
 * @see Type::anyOf()
 *
 * @param callable|Struct ...$validators
 * @return callable
 */
function anyOf(...$validators): callable
{
    return Type::anyOf(...$validators);
}

/**
 * @see Type::arrayOf()
 *
 * @param callable|Struct $validator
 * @return callable
 */
function arrayOf($validator): callable
{
    return Type::arrayOf($validator);
}

/**
 * @see Type::classOf()
 *
 * @param string $type
 * @return callable
 */
function classOf(string $type): callable
{
    return Type::classOf($type);
}

/**
 * @see Type::not()
 *
 * @param callable $validator
 * @return callable
 */
function not(callable $validator): callable
{
    return Type::not($validator);
}

/**
 * @see Type::optional()
 *
 * @param callable $validator
 * @param string $default
 * @return callable
 */
function optional(callable $validator, $default = Missing::class): callable
{
    return Type::optional($validator, $default);
}

/**
 * @see Struct::of()
 *
 * @param string $name Used in error messaging, useful when nesting or validating multiple Structs.
 * @param callable[]|Struct[] $interface A list of expected keys and their associated validator.
 * @param bool $exhaustive Used to specify whether the declared Struct properties are exhaustive,
 * meaning data arrays submitted for validation must not contain unknown keys. This defaults
 * to `true`; Set to `false` if you only want to validate some of the array elements.
 * @return Struct
 */
function struct(string $name, array $interface, bool $exhaustive = true): Struct
{
    return Struct::of($name, $interface, $exhaustive);
}

/**
 * @see Validator::validate()
 *
 * @param array $data
 * @param array|Struct $struct If an array is supplied, a generic, non-exhaustive Struct is used.
 * @return bool
 * @throws Exception\StructValidationException
 */
function validate(array &$data, $struct): bool
{
    return Validator::validate($data, $struct);
}
