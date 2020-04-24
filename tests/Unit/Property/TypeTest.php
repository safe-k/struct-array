<?php

namespace SK\StructArray\Test\Unit\Property;

use PHPUnit\Framework\TestCase;
use SK\StructArray\Property\Missing;
use SK\StructArray\Property\Type;

class TypeTest extends TestCase
{
    /**
     * @dataProvider dataProviderForAllOf
     * @dataProvider dataProviderForAnyOf
     * @dataProvider dataProviderForArrayOf
     * @dataProvider dataProviderForClassOf
     * @dataProvider dataProviderForNot
     * @dataProvider dataProviderForCombo
     */
    public function test($value, $validator, $expected)
    {
        $this->assertEquals($expected, $validator($value));
    }

    public function dataProviderForAllOf(): array
    {
        return [
            // $value, $validator, $expected
            ['123', Type::allOf('is_string', 'is_numeric'), true],
            ['hello', Type::allOf('is_string', 'is_numeric'), false],
            ['not possible', Type::allOf('is_string', 'is_null'), false],
        ];
    }

    public function dataProviderForAnyOf(): array
    {
        return [
            // $value, $validator, $expected
            ['123', Type::anyOf('is_string', 'is_null'), true],
            [true, Type::anyOf('is_string', 'is_null'), false],
            [null, Type::anyOf('is_float', 'is_null'), true],
        ];
    }

    public function dataProviderForArrayOf(): array
    {
        $customValidation = function ($v) {
            return $v === 'hello';
        };

        return [
            // $value, $validator, $expected
            [['hi', 'hello'], Type::arrayOf('is_string'), true],
            [['hi', 1], Type::arrayOf('is_string'), false],
            [[['hi', 'hello'], ['hello']], Type::arrayOf(Type::arrayOf('is_string')), true],
            [['hello', 'hello'], Type::arrayOf($customValidation), true],
            [['hi', 1], Type::arrayOf($customValidation), false],
        ];
    }

    public function dataProviderForClassOf(): array
    {
        return [
            // $value, $validator, $expected
            [new \DateTime(), Type::classOf(\DateTime::class), true],
            ['wut', Type::classOf(\DateTime::class), false],
        ];
    }

    public function dataProviderForNot(): array
    {
        $customValidation = function ($v) {
            return $v === 'hello';
        };

        return [
            // $value, $validator, $expected
            ['hello', Type::not('is_int'), true],
            ['hello', Type::not($customValidation), false],
            [123, Type::not('is_int'), false],
            [null, Type::not('is_null'), false],
            [0, Type::not('is_null'), true],
        ];
    }

    public function dataProviderForCombo(): array
    {
        return [
            // $value, $validator, $expected
            [[new \DateTime(), null], Type::arrayOf(Type::anyOf(Type::classOf(\DateTime::class), 'is_null')), true],
            [['10', 10, null], Type::arrayOf(Type::allOf(Type::not('is_null'), 'is_numeric')), false],
            ['20', Type::optional(Type::not(Type::anyOf('is_null', 'is_bool'))), true],
            [[10, 20, null], Type::arrayOf(Type::anyOf('is_int', 'is_null')), true],
        ];
    }

    /**
     * @dataProvider dataProviderForTestOptional
     */
    public function testOptional($value, $default, $validator, $expectedValue, $expectedValidity)
    {
        $validator = Type::optional($validator, $default);

        $this->assertEquals($expectedValidity, $validator($value));
        $this->assertEquals($expectedValue, $value);
    }

    public function dataProviderForTestOptional(): array
    {
        return [
            // $value, $default, $validator, $expectedValue, $expectedValidity
            [Missing::property('number'), 10, 'is_int', 10, true],
            [Missing::property('number'), Missing::class, 'is_int', Missing::property('number'), true],
            ['hello', Missing::class, 'is_string', 'hello', true],
            [10, Missing::class, 'is_string', 10, false],
            [10, 'hello', 'is_string', 10, false],
        ];
    }
}
