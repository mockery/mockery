<?php

namespace Mockery\Generator;

class MockConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function blackListedMethodsShouldNotBeInListToBeMocked()
    {
        $config = new MockConfiguration(array("Mockery\Generator\\TestSubject"), array("foo"));

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("bar", $methods[0]->getName());
    }

    /**
     * @test
     */
    public function blackListsAreCaseInsensitive()
    {
        $config = new MockConfiguration(array("Mockery\Generator\\TestSubject"), array("FOO"));

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("bar", $methods[0]->getName());
    }


    /**
     * @test
     */
    public function onlyWhiteListedMethodsShouldBeInListToBeMocked()
    {
        $config = new MockConfiguration(array("Mockery\Generator\\TestSubject"), array(), array('foo'));

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("foo", $methods[0]->getName());
    }

    /**
     * @test
     */
    public function whitelistOverRulesBlackList()
    {
        $config = new MockConfiguration(array("Mockery\Generator\\TestSubject"), array("foo"), array("foo"));

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("foo", $methods[0]->getName());
    }

    /**
     * @test
     */
    public function whiteListsAreCaseInsensitive()
    {
        $config = new MockConfiguration(array("Mockery\Generator\\TestSubject"), array(), array("FOO"));

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("foo", $methods[0]->getName());
    }

    /**
     * @test
     */
    public function finalMethodsAreExcluded()
    {
        $config = new MockConfiguration(array("Mockery\Generator\\ClassWithFinalMethod"));

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("bar", $methods[0]->getName());
    }

    /**
     * @test
     */
    public function shouldIncludeMethodsFromAllTargets()
    {
        $config = new MockConfiguration(array("Mockery\\Generator\\TestInterface", "Mockery\\Generator\\TestInterface2"));
        $methods = $config->getMethodsToMock();
        $this->assertEquals(2, count($methods));
    }

    /**
     * @test
     * @expectedException Mockery\Exception
     */
    public function shouldThrowIfTargetClassIsFinal()
    {
        $config = new MockConfiguration(array("Mockery\\Generator\\TestFinal"));
        $config->getTargetClass();
    }

    /**
     * @test
     */
    public function shouldTargetIteratorAggregateIfTryingToMockTraversable()
    {
        $config = new MockConfiguration(array("\\Traversable"));

        $interfaces = $config->getTargetInterfaces();
        $this->assertEquals(1, count($interfaces));
        $first = array_shift($interfaces);
        $this->assertEquals("IteratorAggregate", $first->getName());
    }

    /**
     * @test
     */
    public function shouldTargetIteratorAggregateIfTraversableInTargetsTree()
    {
        $config = new MockConfiguration(array("Mockery\Generator\TestTraversableInterface"));

        $interfaces = $config->getTargetInterfaces();
        $this->assertEquals(2, count($interfaces));
        $this->assertEquals("IteratorAggregate", $interfaces[0]->getName());
        $this->assertEquals("Mockery\Generator\TestTraversableInterface", $interfaces[1]->getName());
    }

    /**
     * @test
     */
    public function shouldBringIteratorToHeadOfTargetListIfTraversablePresent()
    {
        $config = new MockConfiguration(array("Mockery\Generator\TestTraversableInterface2"));

        $interfaces = $config->getTargetInterfaces();
        $this->assertEquals(2, count($interfaces));
        $this->assertEquals("Iterator", $interfaces[0]->getName());
        $this->assertEquals("Mockery\Generator\TestTraversableInterface2", $interfaces[1]->getName());
    }

    /**
     * @test
     */
    public function shouldBringIteratorAggregateToHeadOfTargetListIfTraversablePresent()
    {
        $config = new MockConfiguration(array("Mockery\Generator\TestTraversableInterface3"));

        $interfaces = $config->getTargetInterfaces();
        $this->assertEquals(2, count($interfaces));
        $this->assertEquals("IteratorAggregate", $interfaces[0]->getName());
        $this->assertEquals("Mockery\Generator\TestTraversableInterface3", $interfaces[1]->getName());
    }
}

interface TestTraversableInterface extends \Traversable {}
interface TestTraversableInterface2 extends \Traversable, \Iterator {}
interface TestTraversableInterface3 extends \Traversable, \IteratorAggregate {}

final class TestFinal {}

interface TestInterface
{
    public function foo();
}

interface TestInterface2
{
    public function bar();
}

class TestSubject
{
    public function foo() {}
    public function bar() {}
}

class ClassWithFinalMethod
{
    final public function foo() {}
    public function bar() {}
}
