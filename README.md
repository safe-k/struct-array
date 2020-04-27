![](https://github.com/safe-k/struct-array/workflows/Test/badge.svg)
![GitHub license](https://img.shields.io/github/license/safe-k/struct-array)

# PHP Structured Arrays

Make PHP array data validation easier and clearer by defining array structures.

## Why

We often need to receive, validate, and process data objects. Sometimes, an array might be too
dumb for the task at hand, requiring several unpleasant control structures to validate and setup.
Moreover, creating a dedicated DTO class (or more) might be overkill, requiring a lot of boilerplate
code, whilst making things like error messaging and future refactoring more difficult.

In cases like this I find myself itching for a Go/Rust like `struct` instead.

See [doc](docs/use-case.md) for a more in depth discussion.

## Usage

To define and validate an array data structure, first create a `Struct` - this requires a
`name`, used for error messaging, and an `interface` - then use the provided `Validator`
function to validate the data against the defined `Struct`.

A `Struct`'s `interface` ia an array consisting of string keys (matching the expected property names)
and callable values. These values can be any callable PHP entity, and is expected to accept
a single mixed type `$value`, and return a `bool`. Some valid examples are:
- `is_*` [variable handling functions](https://www.php.net/manual/en/ref.var.php) (`is_string`,
`is_int`, `is_callable` etc.)
- A `Closure` (`function ($value): bool {}`)
- A class with an `__invoke($value): bool` method

### Example

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

For more, see the [examples directory](examples).
