<?php

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsIdentical;

class PhpUnitConstraintExpectationTest extends MockeryTestCase
{
    protected $mock;

    public function mockeryTestSetUp()
    {
        parent::mockeryTestSetUp();
        $this->mock = mock('foo');
    }


    public function mockeryTestTearDown()
    {
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
        parent::mockeryTestTearDown();
    }


    public function testAnythingConstraintMatchesArgument()
    {
        $this->mock->shouldReceive('foo')->with(new IsIdentical(2))->once();
        $this->mock->foo(2);
    }

    public function testGreaterThanConstraintMatchesArgument()
    {
        $this->mock->shouldReceive('foo')->with(new GreaterThan(1))->once();
        $this->mock->foo(2);
    }

    public function testGreaterThanConstraintNotMatchesArgument()
    {
        $this->mock->shouldReceive('foo')->with(new GreaterThan(1));
        $this->expectException(\Mockery\Exception::class);
        $this->mock->foo(1);
        Mockery::close();
    }
}
