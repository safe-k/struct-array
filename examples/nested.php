#!/usr/bin/env php
<?php

require_once __DIR__ . "/../vendor/autoload.php";

use SK\StructArray\Exception\StructValidationException;

use function SK\StructArray\{
    arrayOf, struct, validate
};

$chapterStruct = struct('Chapter', [
    'number' => 'is_int',
    'pages' => 'is_int',
]);

$bookStruct = struct('Book', [
    'title' => 'is_string',
    'chapters' => arrayOf($chapterStruct),
]);

$book = [
    'title' => 'a book',
    'chapters' => [
        [
            'number' => 1,
            'pages' => 50,
        ],
        [
            'number' => 2,
            'pages' => 65,
        ],
        [
            'number' => 3,
            'pages' => 'should be int silly',
        ],
    ],
];

try {
    validate($book, $bookStruct);
} catch (StructValidationException $e) {
    echo "Book is invalid: {$e->getMessage()}\n";
    return;
}

echo "Book '{$book['title']}' is valid: {$e->getMessage()}\n";
