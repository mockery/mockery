<?php

namespace Mockery\Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception\BadMethodCallException;

class MockClassWithMethodOverloadingTest extends MockeryTestCase
{
    public function testCreateMockForClassWithMethodOverloading()
    {
        $mock = mock(TestWithMethodOverloading::class)
            ->makePartial();
        $this->assertInstanceOf(TestWithMethodOverloading::class, $mock);

        $this->assertSame(42, $mock->theAnswer());
    }

    public function testThrowsWhenMethodDoesNotExist()
    {
        $mock = mock(TestWithMethodOverloadingWithoutCall::class)->makePartial();

        $this->assertInstanceOf(TestWithMethodOverloadingWithoutCall::class, $mock);

        $this->expectException(BadMethodCallException::class);

        $mock->randomMethod();
    }

    public function testCreateMockForClassWithMethodOverloadingWithExistingMethod()
    {
        $mock = mock(TestWithMethodOverloading::class)
            ->makePartial();
        $this->assertInstanceOf(TestWithMethodOverloading::class, $mock);

        $this->assertSame(1, $mock->thisIsRealMethod());
    }
}

class TestWithMethodOverloading
{
    public function __call($name, $arguments)
    {
        return 42;
    }

    public function thisIsRealMethod()
    {
        return 1;
    }
}

class TestWithMethodOverloadingWithoutCall
{
}
