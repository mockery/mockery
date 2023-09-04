<?php declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Tests\Unit\PHP80;

use Iterator;
use IteratorAggregate;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP80\ArgumentMixedTypeHint;
use PHP80\ArgumentParentTypeHint;
use PHP80\ArgumentUnionTypeHint;
use PHP80\ArgumentUnionTypeHintWithNull;
use PHP80\ImplementsIterator;
use PHP80\ImplementsIteratorAggregate;
use PHP80\MethodWithStaticReturnType;
use PHP80\ReturnTypeMixedTypeHint;
use PHP80\ReturnTypeParentTypeHint;
use PHP80\ReturnTypeUnionTypeHint;
use stdClass;
use Traversable;

/**
 * @requires PHP 8.0.0-dev
 * @internal
 */
final class Php80LanguageFeaturesTest extends MockeryTestCase
{


    public function testItCanMockAClassWithAMixedArgumentTypeHint(): void
    {
        $mock = mock(ArgumentMixedTypeHint::class);
        $object = new stdClass();
        $mock->allows()->foo($object)->once();

        $mock->foo($object);
    }


    public function testItCanMockAClassWithAMixedReturnTypeHint(): void
    {
        $mock = spy(ReturnTypeMixedTypeHint::class);

        static::assertNull($mock->foo());
    }


    public function testItCanMockAClassWithAParentArgumentTypeHint(): void
    {
        $mock = mock(ArgumentParentTypeHint::class);
        $object = new ArgumentParentTypeHint();
        $mock->allows()->foo($object)->once();

        $mock->foo($object);
    }


    public function testItCanMockAClassWithAParentReturnTypeHint(): void
    {
        $mock = spy(ReturnTypeParentTypeHint::class);

        static::assertInstanceOf(stdClass::class, $mock->foo());
    }


    public function testItCanMockAClassWithAUnionArgumentTypeHint(): void
    {
        $mock = mock(ArgumentUnionTypeHint::class);
        $object = new ArgumentUnionTypeHint();

        $mock->allows()->foo($object)->once();

        $mock->foo($object);
    }


    public function testItCanMockAClassWithAUnionArgumentTypeHintIncludingNull(): void
    {
        $mock = mock(ArgumentUnionTypeHintWithNull::class);

        $mock->allows()->foo(null)->once();

        $mock->foo(null);
    }


    public function testItCanMockAClassWithAUnionReturnTypeHint(): void
    {
        $mock = spy(ReturnTypeUnionTypeHint::class);

        static::assertIsObject($mock->foo());
    }
    public function testMockingIteratorAggregateDoesNotImplementIterator(): void
    {
        $mock = mock(ImplementsIteratorAggregate::class);
        static::assertInstanceOf(IteratorAggregate::class, $mock);
        static::assertInstanceOf(Traversable::class, $mock);
        static::assertNotInstanceOf(Iterator::class, $mock);
    }

    public function testMockingIteratorDoesNotImplementIterator(): void
    {
        $mock = mock(ImplementsIterator::class);
        static::assertInstanceOf(Iterator::class, $mock);
        static::assertInstanceOf(Traversable::class, $mock);
    }

    public function testMockingStaticReturnType(): void
    {
        $mock = mock(MethodWithStaticReturnType::class);

        $mock->shouldReceive("returnType");

        static::assertSame($mock, $mock->returnType());
    }
}
