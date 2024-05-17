# Writing Test Cases

This directory contains test cases for specific issues reported on GitHub. 

## Naming Convention

Each test case should be named according to the associated GitHub issue number.

e.g. `TestCase0001Test.php` is associated with issue `#1`.


## Directory Structure

Each test case should be structured based on the minimum version of php required to run the test case.

```text
tests/
├── Fixture/
│   ├── PHP73/
│   ├── PHP*/
│   ├── PHP84/
│   └── autoload.php
└── Unit/
    ├── PHP73/
    │   ├── TestCase0000Test.php  
    │   └── ...
    ├── PHP74/
    │   ├── TestCase0000Test.php
    │   └── ...
    ├── PHP80/
    │   ├── TestCase0000Test.php
    │   └── ...
    ├── PHP81/
    │   ├── TestCase0000Test.php
    │   └── ...
    ├── PHP82/
    │   ├── TestCase0000Test.php
    │   └── ...
    ├── PHP83/
    │   ├── TestCase0000Test.php
    │   └── ...
    ├── PHP84/
    │   ├── TestCase0000Test.php
    │   └── ...
    └── README.md
```

## Example Test Case

Each test case should extend the `MockeryTestCase` class and include the following annotations:

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\PHP84;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @coversDefaultClass Mockery
 * @requires PHP 8.4
 * @see https://github.com/mockery/mockery/issues/{id}
 */
final class ExampleTestCase extends MockeryTestCase
{
    public function testDescription(): void
    {
        $mock = Mockery::mock('ExampleClass');

        $mock->expects('exampleMethod')->andReturns(true);

        self::assertTrue($mock->exampleMethod());
    }
}
```

## Running Test Cases

To run the test cases, execute the following command:

```command
vendor/bin/phpunit --filter TestCase{id}Test
```
