# Use Case

A common use case for this is if you are expecting to validate a lot of input arguments with
different types and requirements, and then pass them on to some other process; In this scenario,
creating dedicated classes to deal with this might be too much overhead, and using arrays means
you'll need a bunch of `if`s and `switch`es to validate and/or initialise the data.

One such example could be an API endpoint that takes in a bunch of filter parameters, some of them
optional, and returns aggregated statistics.

## Scenario

Let's say your controller endpoint expects a `$filterParams` array with:

- `name string`
- `date DateTime` (optional - defaults today's date)
- `priceRange array`
  - `from float`
  - `to float`
- `tags array<string>` (optional - defaults to empty array)

```php
<?php

// StatsController.php
class StatsController {
    public function index(array $filterParams)
    {
        // TODO: validate params, set any necessary default values, and query the DB

        return DB::action($filterParams);
    }
}
```

### Option 1 - Write a series of `if` statements

A classic.

```php
<?php

// StatsController.php
class StatsController {
    public function index(array $filterParams)
    {
        // Validate `name`
        if (!isset($filterParams['name']) || !is_string($filterParams['name'])) {
            throw new InvalidArgumentException("'name' param must be string");
        }

        // Validate `date`
        if (!!isset($filterParams['date'])) {
            $filterParams['date'] =  new DateTime();
        } elseif (!$filterParams['date'] instanceof DateTime) {
            throw new InvalidArgumentException("'date' param must be an instance of DateTime");
        }

        // Validate `priceRange`
        if (!isset($filterParams['priceRange']) || !is_array($filterParams['priceRange'])) {
            throw new InvalidArgumentException("'priceRange' param must be an array with 'from' and 'to' float values");
        }
        if (!isset($filterParams['priceRange']['from']) || !is_float($filterParams['priceRange']['from'])) {
            throw new InvalidArgumentException("Price range 'from' param must be a float");
        }
        if (!isset($filterParams['priceRange']['to']) || !is_float($filterParams['priceRange']['to'])) {
            throw new InvalidArgumentException("Price range 'to' param must be a float");
        }

        // Validate `tags`
        if (!array_key_exists('tags', $filterParams)) {
            $filterParams['tags'] = [];
        } elseif (!is_array($filterParams['tags'])) {
            throw new InvalidArgumentException("'tags' param must be an array of strings");
        } else {
            $filterParams['tags'] = array_filter($filterParams['tags'], 'is_string');
        }

        return DB::action($filterParams);
    }
}
```

There's a lot to talk about here, but the main points are:

**Pros:**
- Validation rules are defined where the data is received (saves future devs from
having to follow methods and dig through code)
- Precise control over error messaging

**Cons:**
- A lot of noise in the code
- Requires relatively high cognitive load to interpret
- Gets more fragile and inconsistent as it grows

### Option 2 - Build a DTO class

Depending on the context, this might be slightly classier then the previous option:

```php
<?php

declare(strict_types=1);

// Price.php
class Price {
    /** @var float */
    private $from;
    /** @var float */
    private $to;

    public function __construct(float $from, float $to) {
        $this->from = $from;
        $this->to = $to;
    }

    // + necessary accessors
}

// Filter.php
class Filter {
    /** @var string */
    private $name;
    /** @var DateTime */
    private $date;
    /** @var Price */
    private $priceRange;
    /** @var string[] */
    private $tags;

    public function __construct(
        string $name,
        DateTime $date,
        Price $priceRange,
        array $tags
    ) {
        $this->name = $name;
        $this->date = $date;
        $this->priceRange = $priceRange;
        $this->tags = array_filter($tags, 'is_string');
    }

    public static function from(array $params): self
    {
        return new static(
            $params['name'],
            $params['date'] ?? new DateTime(),
            new Price($params['priceRange']['from'], $params['priceRange']['to']),
            $params['tags'] ?? []
        );
    }

    public function toArray(): array
    {
        return []; // TODO
    }

    // + necessary accessors
}

// StatsController.php
class StatsController {
    public function index(array $filterParams)
    {
        return DB::action(Filter::from($filterParams)->toArray());
    }
}
```

**Pros:**
- Easier to read and maintain
- Less reliant on fragile control structures

**Cons:**
- Still involves writing a lot of boilerplate code
- Error messages aren't great; You'll have to catch `TypeError`s and possibly parse them
if you want to return meaningful messages to your user
- For larger, and/or deeply nested data structures, this could increase the amount of code
required, and may even have an impact on performance

## Enter `Struct`

```php
<?php

use function \SK\StructArray\{
    arrayOf, classOf, optional, struct, validate
};

// StatsController.php
class StatsController {
    public function index(array $filterParams)
    {
        validate($filterParams, struct('Filter', [
            'name' => 'is_string',
            'date' => optional(classOf(DateTime::class), new DateTime()),
            'priceRange' => struct('Price', [
                'from' => 'is_float',
                'to' => 'is_float',
            ]),
            'tags' => arrayOf('is_string'),
        ]));

        return DB::action($filterParams);
    }
}
```

**Pros:**
- Easy to read
- Easy to scale
- Automatic, meaningful error messaging; Here's an example:
> Struct 'Filter' failed validation: Invalid value for property 'date'
- All possible parameters and their validation rules are documented in code, in the method itself
- Extensible - `Struct`s use `callable`s (and other `Struct`s) for validation, so it can easily be
worked into an existing system or customised accordingly

**Cons:**
- ???
