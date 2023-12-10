<?php

namespace Mockery\Tests\PHP80;

use ArrayIterator;
use DateTime;
use Iterator;
use IteratorAggregate;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ReturnTypeWillChange;

/**
 * @requires PHP 8.0.0-dev
 */
class Php80LanguageFeaturesTest extends MockeryTestCase
{
    public function testMockingIteratorAggregateDoesNotImplementIterator()
    {
        $mock = mock(ImplementsIteratorAggregate::class);
        $this->assertInstanceOf('IteratorAggregate', $mock);
        $this->assertInstanceOf('Traversable', $mock);
        $this->assertNotInstanceOf('Iterator', $mock);
    }

    public function testMockingIteratorDoesNotImplementIterator()
    {
        $mock = mock(ImplementsIterator::class);
        $this->assertInstanceOf('Iterator', $mock);
        $this->assertInstanceOf('Traversable', $mock);
    }

    /** @test */
    public function it_can_mock_a_class_with_a_mixed_argument_type_hint()
    {
        $mock = mock(ArgumentMixedTypeHint::class);
        $object = new \stdClass();
        $mock->allows()->foo($object)->once();

        $mock->foo($object);
    }

    /** @test */
    public function it_can_mock_a_class_with_a_union_argument_type_hint()
    {
        $mock = mock(ArgumentUnionTypeHint::class);
        $object = new ArgumentUnionTypeHint();
        $mock->allows()->foo($object)->once();

        $mock->foo($object);
    }

    /** @test */
    public function it_can_mock_a_class_with_a_union_argument_type_hint_including_null()
    {
        $mock = mock(ArgumentUnionTypeHintWithNull::class);
        $mock->allows()->foo(null)->once();

        $mock->foo(null);
    }

    /** @test */
    public function it_can_mock_a_class_with_a_parent_argument_type_hint()
    {
        $mock = mock(ArgumentParentTypeHint::class);
        $object = new ArgumentParentTypeHint();
        $mock->allows()->foo($object)->once();

        $mock->foo($object);
    }

    /** @test */
    public function it_can_mock_a_class_with_a_mixed_return_type_hint()
    {
        $mock = spy(ReturnTypeMixedTypeHint::class);

        $this->assertNull($mock->foo());
    }

    /** @test */
    public function it_can_mock_a_class_with_a_union_return_type_hint()
    {
        $mock = spy(ReturnTypeUnionTypeHint::class);

        $this->assertTrue(is_object($mock->foo()));
    }

    /** @test */
    public function it_can_mock_a_class_with_a_parent_return_type_hint()
    {
        $mock = spy(ReturnTypeParentTypeHint::class);

        $this->assertInstanceOf(\stdClass::class, $mock->foo());
    }
}

class ImplementsIteratorAggregate implements IteratorAggregate
{
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator([]);
    }
}

class ImplementsIterator implements Iterator
{
    public function rewind(): void
    {
    }

    public function current(): mixed
    {
    }

    public function key(): mixed
    {
    }

    public function next(): void
    {
    }

    public function valid(): bool
    {
    }
}

class ArgumentMixedTypeHint
{
    public function foo(mixed $foo)
    {
    }
}

class ArgumentUnionTypeHint
{
    public function foo(string|array|self $foo)
    {
    }
}

class ArgumentUnionTypeHintWithNull
{
    public function foo(string|array|null $foo)
    {
    }
}

class ArgumentParentTypeHint extends \stdClass
{
    public function foo(parent $foo)
    {
    }
}

class ReturnTypeMixedTypeHint
{
    public function foo(): mixed
    {
    }
}

class ReturnTypeUnionTypeHint
{
    public function foo(): ReturnTypeMixedTypeHint|self
    {
    }
}

class ReturnTypeParentTypeHint extends \stdClass
{
    public function foo(): parent
    {
    }
}
