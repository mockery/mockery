<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception;

final class HamcrestExpectationTest extends MockeryTestCase
{
    protected $mock;

    public function mockeryTestSetUp()
    {
        parent::mockeryTestSetUp();

        $this->mock = mock('foo');
    }

    public function mockeryTestTearDown()
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);

        parent::mockeryTestTearDown();
    }

    public function testAnythingConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')->with(anything())->once();
        $this->mock->foo(2);
    }

    public function testGreaterThanConstraintMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')->with(greaterThan(1))->once();
        $this->mock->foo(2);
    }

    public function testGreaterThanConstraintNotMatchesArgument(): void
    {
        $this->mock->shouldReceive('foo')->with(greaterThan(1));
        $this->expectException(Exception::class);
        $this->mock->foo(1);
        Mockery::close();
    }
}
