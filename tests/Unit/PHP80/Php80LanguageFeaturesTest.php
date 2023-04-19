<?php

namespace MockeryTest\Unit\PHP80;

use Iterator;
use IteratorAggregate;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use MockeryTest\Fixture\PHP80000\ArgumentMixedTypeHint;
use MockeryTest\Fixture\PHP80000\ArgumentUnionTypeHint;
use MockeryTest\Fixture\PHP80000\ArgumentUnionTypeHintWithNull;
use MockeryTest\Fixture\PHP80000\ImplementsIterator;
use MockeryTest\Fixture\PHP80000\ImplementsIteratorAggregate;
use MockeryTest\Fixture\PHP80000\ReturnTypeParentTypeHint;
use MockeryTest\Fixture\PHP80000\ReturnTypeUnionTypeHint;
use stdClass;
use Traversable;
use function is_object;
use function mock;
use function spy;

/**
 * @requires PHP 8.0.0-dev
 */
class Php80LanguageFeaturesTest extends MockeryTestCase
{
    public function testMockingIteratorAggregateDoesNotImplementIterator()
    {
        $mock = mock(ImplementsIteratorAggregate::class);
        $this->assertInstanceOf(IteratorAggregate::class, $mock);
        $this->assertInstanceOf(Traversable::class, $mock);
        $this->assertNotInstanceOf(Iterator::class, $mock);
    }

    public function testMockingIteratorDoesNotImplementIterator()
    {
        $mock = mock(ImplementsIterator::class);
        $this->assertInstanceOf(Traversable::class, $mock);
        $this->assertInstanceOf(Iterator::class, $mock);
    }

    /** @test */
    public function it_can_mock_a_class_with_a_mixed_argument_type_hint()
    {
        $mock = mock(ArgumentMixedTypeHint::class);
        $object = new stdClass();
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
        $mock = mock(\MockeryTest\Mockery\ArgumentParentTypeHint::class);
        $object = new \MockeryTest\Mockery\ArgumentParentTypeHint();
        $mock->allows()->foo($object)->once();

        $mock->foo($object);
    }

    /** @test */
    public function it_can_mock_a_class_with_a_mixed_return_type_hint()
    {
        $mock = spy(\MockeryTest\Mockery\ReturnTypeMixedTypeHint::class);

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

        $this->assertInstanceOf(stdClass::class, $mock->foo());
    }
}
