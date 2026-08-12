<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Container;
use PHP73\MethodWithNullableReturnType;
use Throwable;
use TypeError;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingNullableMethodsTest extends MockeryTestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @throws Throwable
     */
    public function testItAllowsReturningNullForNullableIntReturnTypes(): void
    {
        $double = Mockery::mock(MethodWithNullableReturnType::class);

        $double->shouldReceive('nullableInt')
            ->andReturnNull();

        self::assertNull($double->nullableInt());
    }

    /**
     * @throws Throwable
     */
    public function testItAllowsReturningNullForNullableObjectReturnTypes(): void
    {
        $double = Mockery::mock(MethodWithNullableReturnType::class);

        $double->shouldReceive('nullableClass')
            ->andReturnNull();

        self::assertNull($double->nullableClass());
    }

    /**
     * @throws Throwable
     */
    public function testItAllowsReturningNullForNullableStringReturnTypes(): void
    {
        $double = Mockery::mock(MethodWithNullableReturnType::class);

        $double->shouldReceive('nullableString')
            ->andReturnNull();

        self::assertNull($double->nullableString());
    }

    /**
     * @throws Throwable
     */
    public function testItReturnsNullOnCallsToIgnoredMethodsOfSpiesIfReturnTypeIsNullable(): void
    {
        $double = Mockery::spy(MethodWithNullableReturnType::class);

        self::assertNull($double->nullableClass());
    }

    /**
     * @throws Throwable
     */
    public function testItShouldAllowClassToBeSet(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nonNullableClass')
            ->andReturn(new MethodWithNullableReturnType())
            ->once();

        $mock->nonNullableClass();
    }

    /**
     * @throws Throwable
     */
    public function testItShouldAllowNonNullableTypeToBeSet(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nonNullablePrimitive')
            ->andReturn('a string')
            ->once();
        $mock->nonNullablePrimitive();
    }

    /**
     * @throws Throwable
     */
    public function testItShouldAllowNullableClassToBeNull(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nullableClass')
            ->andReturn(null)
            ->once();
        $mock->nullableClass();
    }

    /**
     * @throws Throwable
     */
    public function testItShouldAllowNullableSelfToBeNull(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nullableSelf')
            ->andReturn(null)
            ->once();
        $mock->nullableSelf();
    }

    /**
     * @throws Throwable
     */
    public function testItShouldAllowNullableSelfToBeSet(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nullableSelf')
            ->andReturn(new MethodWithNullableReturnType())
            ->once();
        $mock->nullableSelf();
    }

    /**
     * @throws Throwable
     */
    public function testItShouldAllowNullalbeClassToBeSet(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nullableClass')
            ->andReturn(new MethodWithNullableReturnType())
            ->once();
        $mock->nullableClass();
    }

    /**
     * @throws Throwable
     */
    public function testItShouldAllowPrimitiveNullableToBeNull(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nullablePrimitive')
            ->andReturn(null)
            ->once();
        $mock->nullablePrimitive();
    }

    /**
     * @throws Throwable
     */
    public function testItShouldAllowPrimitiveNullableToBeSet(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nullablePrimitive')
            ->andReturn('a string')
            ->once();
        $mock->nullablePrimitive();
    }

    /**
     * @throws Throwable
     */
    public function testItShouldAllowSelfToBeSet(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nonNullableSelf')
            ->andReturn(new MethodWithNullableReturnType())
            ->once();
        $mock->nonNullableSelf();
    }

    /**
     * @throws Throwable
     */
    public function testItShouldNotAllowClassToBeNull(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nonNullableClass')
            ->andReturn(null);
        $this->expectException(TypeError::class);
        $mock->nonNullableClass();
    }

    /**
     * @throws Throwable
     */
    public function testItShouldNotAllowNonNullToBeNull(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nonNullablePrimitive')
            ->andReturn(null);
        $this->expectException(TypeError::class);
        $mock->nonNullablePrimitive();
    }

    /**
     * @throws Throwable
     */
    public function testItShouldNotAllowSelfToBeNull(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nonNullableSelf')
            ->andReturn(null);
        $this->expectException(TypeError::class);
        $mock->nonNullableSelf();
    }
}
