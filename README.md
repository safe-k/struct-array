![](https://github.com/seifkamal/struct-array/workflows/Test/badge.svg)

# PHP Structured Arrays

Make PHP array data validation easier and clearer by defining array structures.

## Why

We often need to receive, validate, and process data objects. Sometimes, an array might be too
dumb for the task at hand, requiring several unpleasant control structures to validate and setup.
Moreover, creating a dedicated DTO class (or more) might be overkill, requiring a lot of boilerplate
code, whilst making things like error messaging and future refactoring more difficult.

In cases like this I find myself itching for a Go/Rust like `struct` instead.

See [doc](docs/use-case.md) for a more in depth discussion.

## Installation

```shell script
composer require seifkamal/struct-array
```

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
         'file' => optional('is_file', __DIR__ . '/README.md'),
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
// Path: /Users/seifkamal/src/struct-array
// File: /Users/seifkamal/src/struct-array/README.md
```

You can also just use an array directly, without creating a `Struct`:

```php
<?php

use function SK\StructArray\{
    arrayOf, optional, not, validate
};

$directory = [...];

validate($directory, [
    'path' => 'is_dir',
    'file' => optional('is_file', __DIR__ . '/README.md'),
    'content' => arrayOf([
        'header' => 'is_string',
        'line' => not('is_null'),
    ]),
]);
```

This is tailored for quick usage, and therefore assumes the defined interface is non-exhaustive
(ie. the array submitted for validation is allowed to have keys that aren't defined here). It also
means error messages will be more generic, ie you'll see:
> Struct failed validation. ...

instead of:
> Directory failed validation. ...

Here's another example directly using the static class methods:

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
     'file' => Type::optional('is_file', __DIR__ . '/README.md'),
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
// Path: /Users/seifkamal/src/struct-array
// File: /Users/seifkamal/src/struct-array/README.md
```

For more, see the [examples directory](examples).
