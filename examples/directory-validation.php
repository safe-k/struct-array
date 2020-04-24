#!/usr/bin/env php
<?php

require_once __DIR__ . "/../vendor/autoload.php";

use SK\StructArray\Exception\StructValidationException;

use function SK\StructArray\{
    arrayOf, optional, not, struct, validate
};

$directoryStruct = struct('Directory', [
    'path' => 'is_dir',
    // Supports optional default values
    'file' => optional('is_file', __DIR__ . '/directory-validation.php'),
    'content' => arrayOf(struct('Paragraph', [
        'header' => 'is_string',
        'line' => not('is_null'),
    ])),
]);

$directory = [
    'path' => __DIR__,
    'content' => [
        [
            'header' => 'Greeting',
            'line' => 'Hello',
        ],
    ]
];

try {
    validate($directory, $directoryStruct);
} catch (StructValidationException $e) {
    echo $e->getMessage() . PHP_EOL;
    return;
}

echo "Path: {$directory['path']}" . PHP_EOL;
echo "File: {$directory['file']}" . PHP_EOL;
