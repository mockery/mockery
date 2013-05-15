<?php

namespace Mockery\Generator;

class MockConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function blackListedMethodsShouldNotBeInListToBeMocked()
    {
        $config = new MockConfiguration;
        $config->addTarget("Mockery\Generator\\TestSubject");
        $config->setBlackListedMethods(array("foo"));

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("bar", $methods[0]->getName());
    }

    /**
     * @test
     */
    public function blackListsAreCaseInsensitive()
    {
        $config = new MockConfiguration;
        $config->addTarget("Mockery\Generator\\TestSubject");
        $config->setBlackListedMethods(array("FOO"));

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("bar", $methods[0]->getName());
    }

    /**
     * @test
     */
    public function reservedWordsAreBlackListedByDefault()
    {
        $config = new MockConfiguration;
        $this->assertContains('abstract', $config->getBlackListedMethods());

        // need a builtin for this
        $this->markTestSkipped("Need a builtin class with a method that is a reserved word");
    }

    /**
     * @test
     */
    public function magicMethodsAreBlackListedByDefault()
    {
        $config = new MockConfiguration;
        $config->addTarget("Mockery\Generator\ClassWithMagicCall");
        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("foo", $methods[0]->getName());
    }

    /**
     * @test
     */
    public function onlyWhiteListedMethodsShouldBeInListToBeMocked()
    {
        $config = new MockConfiguration;
        $config->addTarget("Mockery\Generator\\TestSubject");
        $config->setWhiteListedMethods(array("foo"));

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("foo", $methods[0]->getName());
    }

    /**
     * @test
     */
    public function whitelistOverRulesBlackList()
    {
        $config = new MockConfiguration;
        $config->addTarget("Mockery\Generator\\TestSubject");
        $config->setWhiteListedMethods(array("foo"));
        $config->setBlackListedMethods(array("foo"));

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("foo", $methods[0]->getName());
    }

    /**
     * @test
     */
    public function whiteListsAreCaseInsensitive()
    {
        $config = new MockConfiguration;
        $config->addTarget("Mockery\Generator\\TestSubject");
        $config->setWhiteListedMethods(array("FOO"));

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("foo", $methods[0]->getName());
    }

    /**
     * @test
     */
    public function finalMethodsAreExcluded()
    {
        $config = new MockConfiguration;
        $config->addTarget("Mockery\Generator\\ClassWithFinalMethod");

        $methods = $config->getMethodsToMock();
        $this->assertEquals(1, count($methods));
        $this->assertEquals("bar", $methods[0]->getName());
    }

    /**
     * @test
     */
    public function shouldIncludeMethodsFromAllTargets()
    {
        $config = new MockConfiguration();
        $config->addTarget("Mockery\\Generator\\TestInterface");
        $config->addTarget("Mockery\\Generator\\TestInterface2");
        $methods = $config->getMethodsToMock();
        $this->assertEquals(2, count($methods));
    }

    /**
     * @test
     * @expectedException Mockery\Exception
     */
    public function shouldThrowIfTargetClassIsFinal()
    {
        $config = new MockConfiguration();
        $config->addTarget("Mockery\\Generator\\TestFinal");
        $config->getTargetClass();
    }

    /**
     * @test
     */
    public function shouldTargetIteratorAggregateIfTryingToMockTraversable()
    {
        $config = new MockConfiguration();
        $config->addTarget("\\Traversable");

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
        $config = new MockConfiguration();
        $config->addTarget("Mockery\Generator\TestTraversableInterface");

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
        $config = new MockConfiguration();
        $config->addTarget("Mockery\Generator\TestTraversableInterface2");

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
        $config = new MockConfiguration();
        $config->addTarget("Mockery\Generator\TestTraversableInterface3");

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

class ClassWithMagicCall
{
    public function foo() {}
    public function __call($method, $args) {}
}

