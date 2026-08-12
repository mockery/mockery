<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery\Adapter\Phpunit;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsIdentical;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class PhpUnitConstraintExpectationTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testAnythingConstraintMatchesArgument(): void
    {
        $mock = mock('foo');

        $mock->shouldReceive('foo')
            ->with(new IsIdentical(2))
            ->once();

        $mock->foo(2);
    }

    /**
     * @throws Throwable
     */
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

    /**
     * @throws Throwable
     */
    public function testGreaterThanConstraintMatchesArgument(): void
    {
        $mock = mock('foo');

        $mock->shouldReceive('foo')
            ->with(new GreaterThan(1))
            ->once();

        $mock->foo(2);
    }

    /**
     * @throws Throwable
     */
    public function testGreaterThanConstraintNotMatchesArgument(): void
    {
        $greaterThan = new GreaterThan(1);

        $mock = mock('foo');
        $mock->shouldReceive('foo')
            ->with($greaterThan);

        $this->expectException(Exception::class);

        $mock->foo(1);
    }
}
