<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Container;
use PHP73\MethodWithNullableReturnType;
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

    public function testItAllowsReturningNullForNullableIntReturnTypes(): void
    {
        $double = Mockery::mock(MethodWithNullableReturnType::class);

        $double->shouldReceive('nullableInt')
            ->andReturnNull();

        self::assertNull($double->nullableInt());
    }

    public function testItAllowsReturningNullForNullableObjectReturnTypes(): void
    {
        $double = Mockery::mock(MethodWithNullableReturnType::class);

        $double->shouldReceive('nullableClass')
            ->andReturnNull();

        self::assertNull($double->nullableClass());
    }

    public function testItAllowsReturningNullForNullableStringReturnTypes(): void
    {
        $double = Mockery::mock(MethodWithNullableReturnType::class);

        $double->shouldReceive('nullableString')
            ->andReturnNull();

        self::assertNull($double->nullableString());
    }

    public function testItReturnsNullOnCallsToIgnoredMethodsOfSpiesIfReturnTypeIsNullable(): void
    {
        $double = Mockery::spy(MethodWithNullableReturnType::class);

        self::assertNull($double->nullableClass());
    }

    public function testItShouldAllowClassToBeSet(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nonNullableClass')
            ->andReturn(new MethodWithNullableReturnType())
            ->once();

        $mock->nonNullableClass();
    }

    public function testItShouldAllowNonNullableTypeToBeSet(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nonNullablePrimitive')
            ->andReturn('a string')
            ->once();
        $mock->nonNullablePrimitive();
    }

    public function testItShouldAllowNullableClassToBeNull(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nullableClass')
            ->andReturn(null)
            ->once();
        $mock->nullableClass();
    }

    public function testItShouldAllowNullableSelfToBeNull(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nullableSelf')
            ->andReturn(null)
            ->once();
        $mock->nullableSelf();
    }

    public function testItShouldAllowNullableSelfToBeSet(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nullableSelf')
            ->andReturn(new MethodWithNullableReturnType())
            ->once();
        $mock->nullableSelf();
    }

    public function testItShouldAllowNullalbeClassToBeSet(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nullableClass')
            ->andReturn(new MethodWithNullableReturnType())
            ->once();
        $mock->nullableClass();
    }

    public function testItShouldAllowPrimitiveNullableToBeNull(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nullablePrimitive')
            ->andReturn(null)
            ->once();
        $mock->nullablePrimitive();
    }

    public function testItShouldAllowPrimitiveNullableToBeSet(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nullablePrimitive')
            ->andReturn('a string')
            ->once();
        $mock->nullablePrimitive();
    }

    public function testItShouldAllowSelfToBeSet(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nonNullableSelf')
            ->andReturn(new MethodWithNullableReturnType())
            ->once();
        $mock->nonNullableSelf();
    }

    public function testItShouldNotAllowClassToBeNull(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nonNullableClass')
            ->andReturn(null);
        $this->expectException(TypeError::class);
        $mock->nonNullableClass();
    }

    public function testItShouldNotAllowNonNullToBeNull(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nonNullablePrimitive')
            ->andReturn(null);
        $this->expectException(TypeError::class);
        $mock->nonNullablePrimitive();
    }

    public function testItShouldNotAllowSelfToBeNull(): void
    {
        $mock = mock(MethodWithNullableReturnType::class);

        $mock->shouldReceive('nonNullableSelf')
            ->andReturn(null);
        $this->expectException(TypeError::class);
        $mock->nonNullableSelf();
    }
}
