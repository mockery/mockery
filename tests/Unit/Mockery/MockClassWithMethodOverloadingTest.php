<?php

namespace MockeryTest\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception\BadMethodCallException;
use MockeryTest\Fixture\TestWithMethodOverloading;
use MockeryTest\Fixture\TestWithMethodOverloadingWithoutCall;
use function mock;

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
        $mock = mock(TestWithMethodOverloadingWithoutCall::class)
            ->makePartial();
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
