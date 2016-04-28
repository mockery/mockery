<?php

namespace test\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class MockClassWithMethodOverloadingTest extends MockeryTestCase
{
    private $container;

    protected function setUp()
    {
        $this->container = new \Mockery\Container;
    }

    protected function tearDown()
    {
        $this->container->mockery_close();
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testCreateMockForClassWithMethodOverloading()
    {
        $mock = $this->container->mock('test\Mockery\TestWithMethodOverloading')
            ->makePartial();
        $this->assertInstanceOf('test\Mockery\TestWithMethodOverloading', $mock);

        // TestWithMethodOverloading::__call wouldn't be used. See Gotchas!.
        $mock->randomMethod();
    }

    public function testCreateMockForClassWithMethodOverloadingWithExistingMethod()
    {
        $mock = $this->container->mock('test\Mockery\TestWithMethodOverloading')
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
