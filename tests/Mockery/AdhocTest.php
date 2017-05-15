<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Ad-hoc unit tests for various scenarios reported by users
 */
class Mockery_AdhocTest extends MockeryTestCase
{
    public function setup()
    {
        $this->container = new \Mockery\Container(\Mockery::getDefaultGenerator(), \Mockery::getDefaultLoader());
    }

    public function teardown()
    {
        $this->container->mockery_close();
    }

    public function testSimplestMockCreation()
    {
        $m = $this->container->mock('MockeryTest_NameOfExistingClass');
        $this->assertTrue($m instanceof MockeryTest_NameOfExistingClass);
    }

    public function testMockeryInterfaceForClass()
    {
        $m = $this->container->mock('SplFileInfo');
        $this->assertTrue($m instanceof \Mockery\MockInterface);
    }

    public function testMockeryInterfaceForNonExistingClass()
    {
        $m = $this->container->mock('ABC_IDontExist');
        $this->assertTrue($m instanceof \Mockery\MockInterface);
    }

    public function testMockeryInterfaceForInterface()
    {
        $m = $this->container->mock('MockeryTest_NameOfInterface');
        $this->assertTrue($m instanceof \Mockery\MockInterface);
    }

    public function testMockeryInterfaceForAbstract()
    {
        $m = $this->container->mock('MockeryTest_NameOfAbstract');
        $this->assertTrue($m instanceof \Mockery\MockInterface);
    }

    public function testInvalidCountExceptionThrowsRuntimeExceptionOnIllegalComparativeSymbol()
    {
        $this->expectException('Mockery\Exception\RuntimeException');
        $e = new \Mockery\Exception\InvalidCountException;
        $e->setExpectedCountComparative('X');
    }

    public function testMockeryConstructAndDestructIsNotCalled()
    {
        MockeryTest_NameOfExistingClassWithDestructor::$isDestructorWasCalled = false;
        // We pass no arguments in constructor, so it's not being called. Then destructor shouldn't be called too.
        $this->container->mock('MockeryTest_NameOfExistingClassWithDestructor');
        // Clear references to trigger destructor
        $this->container->mockery_close();
        $this->assertFalse(MockeryTest_NameOfExistingClassWithDestructor::$isDestructorWasCalled);
    }
    public function testMockeryConstructAndDestructIsCalled()
    {
        MockeryTest_NameOfExistingClassWithDestructor::$isDestructorWasCalled = false;
        $this->container->mock('MockeryTest_NameOfExistingClassWithDestructor', array());
        // Clear references to trigger destructor
        $this->container->mockery_close();
        $this->assertTrue(MockeryTest_NameOfExistingClassWithDestructor::$isDestructorWasCalled);
    }

    public function testComparingClassesWithDeprecatedMethods()
    {
        $object1 = new MockeryTest_ClassWithDeprecatedMethods();
        $object2 = new MockeryTest_ClassWithDeprecatedMethods();

        $mock = Mockery::mock('MockeryTest_NameOfInterfaceWithParams');
        $mock->shouldReceive('foo')->with($object1)->andReturn('A');

        $mock->foo($object2);
    }
}

class MockeryTest_NameOfExistingClass
{
}

interface MockeryTest_NameOfInterface
{
    public function foo();
}

interface MockeryTest_NameOfInterfaceWithParams
{
    public function foo(MockeryTest_ClassWithDeprecatedMethods $param);
}

abstract class MockeryTest_NameOfAbstract
{
    abstract public function foo();
}

class MockeryTest_NameOfExistingClassWithDestructor
{
    public static $isDestructorWasCalled = false;

    public function __destruct()
    {
        self::$isDestructorWasCalled = true;
    }
}

class MockeryTest_ClassWithDeprecatedMethods
{
    function getA()
    {
        return 'A';
    }

    /** @deprecated */
    function getDeprecated()
    {
        trigger_error('Here is a problem', E_USER_DEPRECATED);
        return 'ERROR';
    }

    /** @deprecated */
    function isDeprecated()
    {
        trigger_error('Here is a problem', E_USER_DEPRECATED);
        return 'ERROR';
    }
}
