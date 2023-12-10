<?php

declare(strict_types=1);

namespace test\Mockery\Adapter\Phpunit;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsIdentical;

final class PhpUnitConstraintExpectationTest extends MockeryTestCase
{
    public function testAnythingConstraintMatchesArgument(): void
    {
        $mock = mock('foo');

        $mock->shouldReceive('foo')
             ->with(new IsIdentical(2))
             ->once();

        $mock->foo(2);
    }

    public function testGreaterThanConstraintMatchesArgument(): void
    {
        $mock = mock('foo');

        $mock->shouldReceive('foo')
             ->with(new GreaterThan(1))
             ->once();

        $mock->foo(2);
    }

    public function testGreaterThanConstraintNotMatchesArgument(): void
    {
        $greaterThan = new GreaterThan(1);

        $mock = mock('foo');
        $mock->shouldReceive('foo')
             ->with($greaterThan);

        $this->expectException(Exception::class);

        $mock->foo(1);
    }

    public function testConstraintExceptionMessage(): void
    {
        self::markTestSkipped('TODO: Constraint exception message');

        // Expected: Failed asserting that {actual} is greater than 1
        // Actual: No matching handler found for Mockery_2__foo::foo(1).
        //         Either the method was unexpected or its arguments matched
        //         no expected argument list for this method
        
        $greaterThan = new GreaterThan(1);

        $mock = mock('foo');
        $mock->shouldReceive('foo')
             ->with($greaterThan);

        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches($greaterThan->toString());

        $mock->foo(1);
    }
}
