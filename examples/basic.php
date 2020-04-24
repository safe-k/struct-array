#!/usr/bin/env php
<?php

require_once __DIR__ . "/../vendor/autoload.php";

use SK\StructArray\Exception\StructValidationException;

use function SK\StructArray\{
    allOf, anyOf, arrayOf, classOf, not, struct, validate
};

$eventStruct = struct('Event', [
    'id' => allOf('is_string', 'is_numeric'),
    'type' => 'is_string',
    'date' => anyOf(classOf(DateTime::class), 'is_null'),
    'priceFrom' => 'is_float',
    'tickets' => arrayOf(not('is_null')),
    'tagMap' => arrayOf(arrayOf('is_string')),
]);

$events = [
    [
        'id' => '123',
        'type' => 'theatre',
        'date' => new DateTime(),
        'priceFrom' => 20.5,
        'tickets' => ['General', 10],
        'tagMap' => [['family', 'kids'], ['gig', 'club']],
    ],
    [
        'id' => '123',
        'type' => 'theatre',
        'date' => new DateTime(),
        'priceFrom' => 20.5,
        'tickets' => ['General', 10],
        'tagMap' => ['family', 'kids'],
    ],
    [
        'id' => '123',
        'type' => 'sport',
        'date' => '02-02-2020',
        'priceFrom' => 20.5,
        'tickets' => [],
        'tagMap' => [],
    ],
    [
        'id' => 'abc',
        'type' => 'sport',
        'date' => new DateTime(),
        'priceFrom' => 20.5,
        'tickets' => [],
        'tagMap' => [],
    ],
    [
        'id' => '123',
        'type' => null,
        'date' => new DateTime(),
        'priceFrom' => 20.5,
        'tickets' => ['General', null],
        'tagMap' => [],
    ],
    [
        'id' => '123',
        'type' => 'theatre',
        'date' => null,
        'priceFrom' => 20.5,
        'tickets' => ['General', 10],
        'what is this' => 'get out',
        'tagMap' => [],
    ],
];

foreach ($events as $id => $event) {
    try {
        validate($event, $eventStruct);
    } catch (StructValidationException $e) {
        echo $e->getMessage() . PHP_EOL;
    }
}
