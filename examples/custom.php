#!/usr/bin/env php
<?php

require_once __DIR__ . "/../vendor/autoload.php";

use SK\StructArray\Exception\StructValidationException;

use function SK\StructArray\{struct, validate};

$message = [
    'subject' => 'Greetings',
    'body' => 'hi',
];

try {
    validate($message, struct('Message', [
        'subject' => 'is_string',
        'body' => function ($value): bool {
            return in_array($value, ['hello', 'hi', 'hey']);
        },
    ]));
} catch (StructValidationException $e) {
    echo "Message is invalid: {$e->getMessage()}" . PHP_EOL;
    return;
}

echo 'Message is valid' . PHP_EOL;
