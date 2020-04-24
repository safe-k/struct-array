<?php

namespace SK\StructArray;

use SK\StructArray\Property\Missing;
use SK\StructArray\Property\Type;

/**
 * @see Type::allOf
 *
 * @param callable[] $validators
 * @return callable
 */
function allOf(callable ...$validators): callable
{
    return Type::allOf(...$validators);
}

/**
 * @see Type::anyOf
 *
 * @param callable|Struct ...$validators
 * @return callable
 */
function anyOf(...$validators): callable
{
    return Type::anyOf(...$validators);
}

/**
 * @see Type::arrayOf
 *
 * @param callable|Struct $validator
 * @return callable
 */
function arrayOf($validator): callable
{
    return Type::arrayOf($validator);
}

/**
 * @see Type::classOf
 *
 * @param string $type
 * @return callable
 */
function classOf(string $type): callable
{
    return Type::classOf($type);
}

/**
 * @see Type::not
 *
 * @param callable $validator
 * @return callable
 */
function not(callable $validator): callable
{
    return Type::not($validator);
}

/**
 * @see Type::optional
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
 * @see Struct::of
 *
 * @param string $name
 * @param callable|Struct[] $interface
 * @param bool $exhaustive
 * @return Struct
 */
function struct(string $name, array $interface, bool $exhaustive = true): Struct
{
    return Struct::of($name, $interface, $exhaustive);
}

/**
 * @see Validator::validate
 *
 * @param array $data
 * @param Struct $struct
 * @return bool
 * @throws Exception\StructValidationException
 */
function validate(array &$data, Struct $struct): bool
{
    return Validator::validate($data, $struct);
}
