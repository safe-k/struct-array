<?php

namespace SK\StructArray\Test\Unit;

use PHPUnit\Framework\TestCase;
use SK\StructArray\Exception;
use SK\StructArray\Exception\StructValidationException;
use SK\StructArray\Property\Type;
use SK\StructArray\Struct;

use function SK\StructArray\validate;

class StructTest extends TestCase
{
    /**
     * @dataProvider dataProviderForTest
     */
    public function test($data, $interface, $exhaustive, $expectedException)
    {
        if ($expectedException) {
            $this->expectException(StructValidationException::class);
        }

        $this->assertTrue(validate($data, Struct::of('Test', $interface, $exhaustive)));
    }

    public function dataProviderForTest(): array
    {
        return [
            [
                ['name' => 'toasty',],
                ['name' => 'invalid validator'],
                true,
                true,
            ],
            [
                ['name' => 10],
                ['name' => 'is_string'],
                true,
                true,
            ],
            [
                [],
                ['name' => 'is_string'],
                true,
                true,
            ],
            [
                ['name' => 'toasty', 'age' => 10],
                ['name' => 'is_string'],
                true,
                true,
            ],
            [
                [
                    'id' => '123',
                    'type' => 'theatre',
                    'date' => new \DateTime(),
                    'price' => [
                        'value' => 20.5,
                        'currency' => 'GBP',
                    ],
                    'tickets' => ['General', 10],
                    'onSale' => true,
                    'artist' => 'some guy',
                ],
                [
                    'id' => Type::allOf('is_string', 'is_numeric'),
                    'type' => 'is_string',
                    'date' => Type::anyOf(Type::classOf(\DateTime::class), 'is_null'),
                    'price' => Struct::of('Price', [
                        'value' => 'is_float',
                        'currency' => 'is_string'
                    ]),
                    'tickets' => Type::arrayOf(Type::not('is_null')),
                ],
                false,
                null
            ],
        ];
    }
}
