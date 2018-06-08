<?php

namespace test\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class MockClassWithMethodOverloadingTest extends MockeryTestCase
{
    /**
     * @expectedException BadMethodCallException
     */
    public function testCreateMockForClassWithMethodOverloading()
    {
        $mock = mock('test\Mockery\TestWithMethodOverloading')
            ->makePartial();
        $this->assertInstanceOf('test\Mockery\TestWithMethodOverloading', $mock);

        // TestWithMethodOverloading::__call wouldn't be used. See Gotchas!.
        $mock->randomMethod();
    }

    public function testCreateMockForClassWithMethodOverloadingWithExistingMethod()
    {
        $mock = mock('test\Mockery\TestWithMethodOverloading')
            ->makePartial();
        $this->assertInstanceOf('test\Mockery\TestWithMethodOverloading', $mock);

        $this->assertSame(1, $mock->thisIsRealMethod());
    }
}

class TestWithMethodOverloading
{
    public function __call($name, $arguments)
    {
        return 1;
    }

    public function thisIsRealMethod()
    {
        return 1;
    }
}
