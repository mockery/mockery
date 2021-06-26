<?php

namespace test\Mockery;

use DateTime;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ReturnTypeWillChange;

/**
 * @requires PHP 8.0.0-dev
 */
class Php80LanguageFeaturesTest extends MockeryTestCase
{
    /** @test */
    public function it_can_mock_a_class_with_a_mixed_argument_type_hint()
    {
        $mock = mock(ArgumentMixedTypeHint::class);
        $object = new \stdClass();
        $mock->allows()->foo($object);

        $mock->foo($object);
    }

    /** @test */
    public function it_can_mock_a_class_with_a_union_argument_type_hint()
    {
        $mock = mock(ArgumentUnionTypeHint::class);
        $object = new ArgumentUnionTypeHint();
        $mock->allows()->foo($object);

        $mock->foo($object);
    }

    /** @test */
    public function it_can_mock_a_class_with_a_union_argument_type_hint_including_null()
    {
        $mock = mock(ArgumentUnionTypeHintWithNull::class);
        $mock->allows()->foo(null);

        $mock->foo(null);
    }

    /** @test */
    public function it_can_mock_a_class_with_a_parent_argument_type_hint()
    {
        $mock = mock(ArgumentParentTypeHint::class);
        $object = new ArgumentParentTypeHint();
        $mock->allows()->foo($object);

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

    /**
     * @test
     * @requires PHP 8.1
     */
    public function it_can_mock_an_internal_class_with_tentative_return_types()
    {
        $mock = spy(DateTime::class);

        $this->assertSame(0, $mock->getTimestamp());
    }

    /** @test */
    public function it_can_mock_a_class_with_return_type_will_change_attribute_and_no_return_type()
    {
        $mock = spy(ReturnTypeWillChangeAttributeNoReturnType::class);

        $this->assertNull($mock->getTimestamp());
    }

    /** @test */
    public function it_can_mock_a_class_with_return_type_will_change_attribute_and_wrong_return_type()
    {
        $mock = spy(ReturnTypeWillChangeAttributeWrongReturnType::class);

        $this->assertSame(0.0, $mock->getTimestamp());
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

class ReturnTypeWillChangeAttributeNoReturnType extends DateTime
{
    #[ReturnTypeWillChange]
    public function getTimestamp()
    {
    }
}

class ReturnTypeWillChangeAttributeWrongReturnType extends DateTime
{
    #[ReturnTypeWillChange]
    public function getTimestamp(): float
    {
    }
}
