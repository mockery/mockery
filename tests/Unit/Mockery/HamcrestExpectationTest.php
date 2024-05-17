<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception;

use function anything;
use function greaterThan;
use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class HamcrestExpectationTest extends MockeryTestCase
{
    protected $mock;

    protected function mockeryTestSetUp(): void
    {
        $this->mock = mock('foo');
    }

    public function mockeryTestTearDown(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    public function testAnythingConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(anything())
            ->once();
        $this->mock->foo(2);
    }

    public function testGreaterThanConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(greaterThan(1))
            ->once();
        $this->mock->foo(2);
    }

    public function testGreaterThanConstraintNotMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')
            ->with(greaterThan(1));
        $this->expectException(Exception::class);
        $this->mock->foo(1);
        Mockery::close();
    }
}
