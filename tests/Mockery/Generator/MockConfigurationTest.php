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
    }

}

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

