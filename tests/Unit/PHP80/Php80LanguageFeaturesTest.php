<?php

declare(strict_types=1);

namespace Tests\Unit\PHP80;

use Iterator;
use IteratorAggregate;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP80\ArgumentMixedTypeHint;
use PHP80\ArgumentParentTypeHint;
use PHP80\ArgumentUnionTypeHint;
use PHP80\ArgumentUnionTypeHintWithNull;
use PHP80\ImplementsIterator;
use PHP80\ImplementsIteratorAggregate;
use PHP80\ReturnTypeMixedTypeHint;
use PHP80\ReturnTypeParentTypeHint;
use PHP80\ReturnTypeUnionTypeHint;
use stdClass;
use Traversable;

use function mock;
use function spy;

/**
 * @requires PHP 8.0.0-dev
 * @coversDefaultClass \Mockery
 */
final class Php80LanguageFeaturesTest extends MockeryTestCase
{
    public function testItCanMockAClassWithAMixedArgumentTypeHint(): void
    {
        $mock = mock(ArgumentMixedTypeHint::class);
        $object = new stdClass();
        $mock->allows()
            ->foo($object)
            ->once();

        $mock->foo($object);
    }

    public function testItCanMockAClassWithAMixedReturnTypeHint(): void
    {
        $mock = spy(ReturnTypeMixedTypeHint::class);

        self::assertNull($mock->foo());
    }

    public function testItCanMockAClassWithAParentArgumentTypeHint(): void
    {
        $mock = mock(ArgumentParentTypeHint::class);
        $object = new ArgumentParentTypeHint();
        $mock->allows()
            ->foo($object)
            ->once();

        $mock->foo($object);
    }

    public function testItCanMockAClassWithAParentReturnTypeHint(): void
    {
        $mock = spy(ReturnTypeParentTypeHint::class);

        self::assertInstanceOf(stdClass::class, $mock->foo());
    }

    public function testItCanMockAClassWithAUnionArgumentTypeHint(): void
    {
        $mock = mock(ArgumentUnionTypeHint::class);
        $object = new ArgumentUnionTypeHint();
        $mock->allows()
            ->foo($object)
            ->once();

        $mock->foo($object);
    }

    public function testItCanMockAClassWithAUnionArgumentTypeHintIncludingNull(): void
    {
        $mock = mock(ArgumentUnionTypeHintWithNull::class);
        $mock->allows()
            ->foo(null)
            ->once();

        $mock->foo(null);
    }

    public function testItCanMockAClassWithAUnionReturnTypeHint(): void
    {
        $mock = spy(ReturnTypeUnionTypeHint::class);

        self::assertIsObject($mock->foo());
    }

    public function testMockingIteratorAggregateDoesNotImplementIterator(): void
    {
        $mock = mock(ImplementsIteratorAggregate::class);
        self::assertInstanceOf(IteratorAggregate::class, $mock);
        self::assertInstanceOf(Traversable::class, $mock);
        self::assertNotInstanceOf(Iterator::class, $mock);
    }

    public function testMockingIteratorDoesNotImplementIterator(): void
    {
        $mock = mock(ImplementsIterator::class);
        self::assertInstanceOf(Iterator::class, $mock);
        self::assertInstanceOf(Traversable::class, $mock);
    }
}
