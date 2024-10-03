<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception\BadMethodCallException;
use PHP73\TestWithMethodOverloading;
use PHP73\TestWithMethodOverloadingWithoutCall;

/**
 * @coversDefaultClass \Mockery
 */
final class MockClassWithMethodOverloadingTest extends MockeryTestCase
{
    public function testCreateMockForClassWithMethodOverloading(): void
    {
        $mock = \mock(TestWithMethodOverloading::class)->makePartial();

        self::assertInstanceOf(TestWithMethodOverloading::class, $mock);

        self::assertSame(42, $mock->theAnswer());
    }

    public function testCreateMockForClassWithMethodOverloadingWithExistingMethod(): void
    {
        $mock = \mock(TestWithMethodOverloading::class)->makePartial();

        self::assertInstanceOf(TestWithMethodOverloading::class, $mock);

        self::assertSame(1, $mock->thisIsRealMethod());
    }

    public function testThrowsWhenMethodDoesNotExist(): void
    {
        $mock = \mock(TestWithMethodOverloadingWithoutCall::class)->makePartial();

        self::assertInstanceOf(TestWithMethodOverloadingWithoutCall::class, $mock);

        $this->expectException(BadMethodCallException::class);

        $mock->randomMethod();
    }
}
