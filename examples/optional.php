<?php

require_once __DIR__ . "/../vendor/autoload.php";

use SK\StructArray\Exception\StructValidationException;

use function SK\StructArray\{
    optional, struct, validate
};

$aussie = ['name' => 'Brenton'];

try {
    validate($aussie, struct('Aussie', [
        'name' => 'is_string',
        'worries' => optional('is_string', 'no'),
    ]));
} catch (StructValidationException $e) {
    echo "Brenton's got some worries: {$e->getMessage()}" . PHP_EOL;
    return;
}

echo "Brenton's got {$aussie['worries']} worries" . PHP_EOL;
