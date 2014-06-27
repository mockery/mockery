<?php

namespace Mockery;

use Mockery\Generator\DefinedTargetClass;

class DefinedTargetClassTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_knows_if_one_of_its_ancestors_is_internal()
    {
        $target = new DefinedTargetClass(new \ReflectionClass("ArrayObject"));
        $this->assertTrue($target->hasInternalAncestor());

        $target = new DefinedTargetClass(new \ReflectionClass("Mockery\MockeryTest_ClassThatExtendsArrayObject"));
        $this->assertTrue($target->hasInternalAncestor());

        $target = new DefinedTargetClass(new \ReflectionClass("Mockery\DefinedTargetClassTest"));
        $this->assertFalse($target->hasInternalAncestor());
    }
}

class MockeryTest_ClassThatExtendsArrayObject extends \ArrayObject {}
