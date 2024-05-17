<?php

declare(strict_types=1);

namespace Tests\Unit\PHP81;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @coversDefaultClass Mockery
 * @requires PHP 8.1
 * @see https://github.com/mockery/mockery/issues/{id}
 */
final class ExampleTestCase0000Test extends MockeryTestCase
{
    public function testDescription(): void
    {
        $mock = Mockery::mock('ExampleClass');

        $mock->expects('exampleMethod')->andReturns(true);

        self::assertTrue($mock->exampleMethod());
    }
}
