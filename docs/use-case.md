# Use Case

A common use case for this is if you are expecting to validate a lot of input arguments with
different types and requirements, and then pass them on to some other process; In this scenario,
creating dedicated classes to deal with this might be too much overhead, and using arrays means
you'll need a bunch of `if`s and `switch`es to validate and/or initialise the data.

One such example could be an API endpoint that takes in a bunch of filter parameters, some of them
optional, and returns aggregated statistics.

## Scenario

Let's say your controller endpoint expects a `$filterParams` array containing some event data:

- `name string`
- `category string` (one of 'theatre', 'concert', or 'sport')
- `date DateTime` (optional - defaults today's date)
- `priceRange array`
  - `from float`
  - `to float`

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

        // Validate `category`
        $categories = ['theatre', 'concert', 'sport'];
        if (!isset($filterParams['category']) || !in_array($filterParams['category'], $categories)) {
            throw new InvalidArgumentException("'category' param must be one of: " . implode(', ', $categories));
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

// Event.php
class Event {
    const CATEGORIES = ['theatre', 'concert', 'sport'];

    /** @var string */
    private $name;
    /** @var string */
    private $category;
    /** @var DateTime */
    private $date;
    /** @var Price */
    private $priceRange;

    public function __construct(
        string $name,
        string $category,
        DateTime $date,
        Price $priceRange
    ) {
        $this->name = $name;
        if (!in_array($category, self::CATEGORIES)) {
            throw new InvalidArgumentException("'category' param must be one of: " . implode(', ', self::CATEGORIES);
        }
        $this->category = $category;
        $this->date = $date;
        $this->priceRange = $priceRange;
    }

    public static function from(array $params): self
    {
        return new static(
            $params['name'],
            $params['category'],
            $params['date'] ?? new DateTime(),
            new Price($params['priceRange']['from'], $params['priceRange']['to'])
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
        return DB::action(Event::from($filterParams)->toArray());
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

// StatsController.php
use function \SK\StructArray\{
    classOf, optional, struct, validate
};

class StatsController {
    public function index(array $filterParams)
    {
        validate($filterParams, struct('Event', [
            'name' => 'is_string',
            'category' => function (string $value): bool {
                return in_array($value, ['theatre', 'concert', 'sport']);
            },
            'date' => optional(classOf(DateTime::class), new DateTime()),
            'priceRange' => struct('Price', [
                'from' => 'is_float',
                'to' => 'is_float',
            ]),
        ]));

        return DB::action($filterParams);
    }
}
```

**Pros:**
- Easy to read
- Easy to scale
- Automatic, customisable error messaging; Here's an example:
> Event failed validation. Invalid value for property: 'date'
- All possible parameters and their validation rules are documented in code, in the method itself
- Extensible - `Struct`s are essentially arrays of `callable`s (and other `Struct`s), so they can
easily be worked into systems and be extended accordingly

**Cons:**
- ???
