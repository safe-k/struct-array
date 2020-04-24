![GitHub license](https://img.shields.io/github/license/safe-k/struct-array)

# PHP Structured Arrays

Make PHP array data validation easier and clearer by defining array structures.

## Why

We often need to receive, validate, and process data objects. Sometimes, an array might be too
dumb for the task at hand, requiring several unpleasant control structures to validate and setup.
Moreover, creating a dedicated DTO class (or more) might be overkill, requiring a lot of boilerplate
code, whilst making things like error messaging and future refactoring more difficult.

In cases like this I find myself itching for a Go/Rust like `Struct` instead.

See [doc](docs/use-case.md) for a more in depth discussion.

## Usage

Here's an example written using the provided convenient helper functions:

```php
<?php

use SK\StructArray\Exception\StructValidationException;

use function SK\StructArray\{
    arrayOf, optional, not, struct, validate
};

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
    validate($directory, struct('Directory', [
         'path' => 'is_dir',
         'file' => optional('is_file', __DIR__ . '/directory-validation.php'),
         'content' => arrayOf(struct('Paragraph', [
             'header' => 'is_string',
             'line' => not('is_null'),
         ])),
     ]));
} catch (StructValidationException $e) {
    echo $e->getMessage() . PHP_EOL;
    return;
}

echo "Path: {$directory['path']}" . PHP_EOL;
echo "File: {$directory['file']}" . PHP_EOL;
// Prints:
// Path: /Users/seifkamal/src/struct-array/examples
// File: /Users/seifkamal/src/struct-array/examples/directory-validation.php
```

Here's the same one using static class methods:

```php
<?php

use SK\StructArray\Exception\StructValidationException;
use SK\StructArray\Property\Type;
use SK\StructArray\Struct;
use SK\StructArray\Validator;

$directory = [
    'path' => __DIR__,
    'content' => [
        [
            'header' => 'Greeting',
            'line' => 'Hello',
        ],
    ]
];

$paragraphStruct = Struct::of('Paragraph', [
     'header' => 'is_string',
     'line' => Type::not('is_null'),
]);
$directoryStruct = Struct::of('Directory', [
     'path' => 'is_dir',
     'file' => Type::optional('is_file', __DIR__ . '/directory-validation.php'),
     'content' => Type::arrayOf($paragraphStruct),
]);

try {
    Validator::validate($directory, $directoryStruct);
} catch (StructValidationException $e) {
    echo $e->getMessage() . PHP_EOL;
    return;
}

echo "Path: {$directory['path']}" . PHP_EOL;
echo "File: {$directory['file']}" . PHP_EOL;
// Prints:
// Path: /Users/seifkamal/src/struct-array/examples
// File: /Users/seifkamal/src/struct-array/examples/directory-validation.php
```

For more detailed ones, see the [examples directory](examples).
