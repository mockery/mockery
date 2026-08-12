<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

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
use PHP80\MultiArgument;
use PHP80\ReturnTypeMixedTypeHint;
use PHP80\ReturnTypeParentTypeHint;
use PHP80\ReturnTypeUnionTypeHint;
use stdClass;
use Throwable;
use Traversable;

use function mock;
use function spy;

/**
 * @requires PHP 8.0.0-dev
 *
 * @coversDefaultClass \Mockery
 */
final class Php80LanguageFeaturesTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithAMixedArgumentTypeHint(): void
    {
        $mock = mock(ArgumentMixedTypeHint::class);
        $object = new stdClass();
        $mock->allows()
            ->foo($object)
            ->once();

        $mock->foo($object);
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithAMixedReturnTypeHint(): void
    {
        $mock = spy(ReturnTypeMixedTypeHint::class);

        self::assertNull($mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithANamedArgumentList(): void
    {
        $mock = mock(MultiArgument::class);

        $mock->allows()->foo(bar: 1, dol: '1')->times(3);

        $mock->foo(bar: 1, dol: '1');
        $mock->foo(bar: 1, bee: '', dol: '1');
        $mock->foo(1, '', '1');

        $mock->allows()->foo(bee: '1')->times(3);

        $mock->foo(bee: '1');
        $mock->foo(bar: 0, bee: '1');
        $mock->foo(0, '1');

        $mock->allows()->foo(bar: 1)->times(2);

        $mock->foo(bar: 1);
        $mock->foo(1);
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithANamedArgumentListWithoutDefaultValue(): void
    {
        $mock = mock(MultiArgument::class);

        $mock->expects('bar')->with(bool: true, int: 1, string: '')->times(3);

        $mock->bar(1, '', true);

        $mock->bar(bool: true, int: 1, string: '');

        $mock->bar(string: '', bool: true, int: 1);
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithAParentArgumentTypeHint(): void
    {
        $mock = mock(ArgumentParentTypeHint::class);
        $object = new ArgumentParentTypeHint();
        $mock->allows()
            ->foo($object)
            ->once();

        $mock->foo($object);
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithAParentReturnTypeHint(): void
    {
        $mock = spy(ReturnTypeParentTypeHint::class);

        self::assertInstanceOf(stdClass::class, $mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithAUnionArgumentTypeHint(): void
    {
        $mock = mock(ArgumentUnionTypeHint::class);
        $object = new ArgumentUnionTypeHint();
        $mock->allows()
            ->foo($object)
            ->once();

        $mock->foo($object);
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithAUnionArgumentTypeHintIncludingNull(): void
    {
        $mock = mock(ArgumentUnionTypeHintWithNull::class);
        $mock->allows()
            ->foo(null)
            ->once();

        $mock->foo(null);
    }

    /**
     * @throws Throwable
     */
    public function testItCanMockAClassWithAUnionReturnTypeHint(): void
    {
        $mock = spy(ReturnTypeUnionTypeHint::class);

        self::assertIsObject($mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testItCanSpyAClassWithANamedArgumentList(): void
    {
        $spy = spy(MultiArgument::class);

        $param = [
            'bar' => 2,
            'dol' => '2',
        ];
        $spy->foo(...$param);

        $spy->shouldHaveReceived(method: 'foo', args: $param);
        $spy->shouldHaveReceived(method: 'foo', args: [
            'bar' => 2,
            'bee' => '',
            'dol' => '2',
        ]);
        $spy->shouldHaveReceived(method: 'foo', args: [2, '', '2']);

        $param = [
            'bee' => '2',
        ];
        $spy->foo(...$param);

        $spy->shouldHaveReceived(method: 'foo', args: $param);
        $spy->shouldHaveReceived(method: 'foo', args: [
            'bar' => 0,
            'bee' => '2',
        ]);
        $spy->shouldHaveReceived(method: 'foo', args: [0, '2']);

        $param = [
            'bar' => 2,
        ];
        $spy->foo(...$param);
        $spy->shouldHaveReceived(method: 'foo', args: $param);
        $spy->shouldHaveReceived(method: 'foo', args: [2]);
    }

    /**
     * @throws Throwable
     */
    public function testMockingIteratorAggregateDoesNotImplementIterator(): void
    {
        $mock = mock(ImplementsIteratorAggregate::class);
        self::assertInstanceOf(IteratorAggregate::class, $mock);
        self::assertInstanceOf(Traversable::class, $mock);
        self::assertNotInstanceOf(Iterator::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testMockingIteratorDoesNotImplementIterator(): void
    {
        $mock = mock(ImplementsIterator::class);
        self::assertInstanceOf(Iterator::class, $mock);
        self::assertInstanceOf(Traversable::class, $mock);
    }
}
