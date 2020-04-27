#!/usr/bin/env php
<?php

require_once __DIR__ . "/../vendor/autoload.php";

use SK\StructArray\Exception\StructValidationException;

use function SK\StructArray\{struct, validate};

$message = [
    'subject' => 'Greetings',
    'body' => 'yo',
];

try {
    validate($message, struct('Message', [
        'subject' => 'is_string',
        'body' => function ($value): bool {
            $greetings = ['hello', 'hi', 'hey'];
            if (!in_array($value, $greetings)) {
                throw new InvalidArgumentException('Greeting body must be one of: ' . implode(', ', $greetings));
            }
            return true;
        },
    ]));
} catch (StructValidationException $e) {
    echo "Message is invalid: {$e->getMessage()}" . PHP_EOL;
    return;
}

echo 'Message is valid' . PHP_EOL;
