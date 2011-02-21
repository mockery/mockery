<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/master/LICENSE
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

class ContainerTest extends PHPUnit_Framework_TestCase
{

    public function setup ()
    {
        $this->container = new \Mockery\Container;
    }
    
    public function teardown()
    {
        $this->container->mockery_close();
    }

    public function testSimplestMockCreation()
    {
        $m = $this->container->mock();
        $m->shouldReceive('foo')->andReturn('bar');
        $this->assertEquals('bar', $m->foo());
    }
    
    public function testNamedMocksAddNameToExceptions()
    {
        $m = $this->container->mock('Foo');
        $m->shouldReceive('foo')->with(1)->andReturn('bar');
        try {
            $m->foo();
        } catch (\Mockery\Exception $e) {
            $this->assertTrue((bool) preg_match("/Foo/", $e->getMessage()));
        }
    }
    
    public function testSimpleMockWithArrayDefs()
    {
        $m = $this->container->mock(array('foo'=>1,'bar'=>2));
        $this->assertEquals(1, $m->foo());
        $this->assertEquals(2, $m->bar());
    }
    
    public function testNamedMockWithArrayDefs()
    {
        $m = $this->container->mock('Foo', array('foo'=>1,'bar'=>2));
        $this->assertEquals(1, $m->foo());
        $this->assertEquals(2, $m->bar());
        try {
            $m->f();
        } catch (BadMethodCallException $e) {
            $this->assertTrue((bool) preg_match("/Foo/", $e->getMessage()));
        }
    }
    
    public function testMockingAKnownConcreteClassSoMockInheritsClassType()
    {
        $m = $this->container->mock('stdClass');
        $m->shouldReceive('foo')->andReturn('bar');
        $this->assertEquals('bar', $m->foo());
        $this->assertTrue($m instanceof stdClass);
    }
    
    public function testMockingAConcreteObjectCreatesAPartialWithoutError()
    {
        $m = $this->container->mock(new stdClass);
        $m->shouldReceive('foo')->andReturn('bar');
        $this->assertEquals('bar', $m->foo());
        $this->assertTrue($m instanceof stdClass);
    }
    
    public function testCreatingAPartialAllowsDynamicExpectationsAndPassesThroughUnexpectedMethods()
    {
        $m = $this->container->mock(new MockeryTestFoo);
        $m->shouldReceive('bar')->andReturn('bar');
        $this->assertEquals('bar', $m->bar());
        $this->assertEquals('foo', $m->foo());
        $this->assertTrue($m instanceof MockeryTestFoo);
    }
    
    public function testCreatingAPartialAllowsExpectationsToInterceptCallsToImplementedMethods()
    {
        $m = $this->container->mock(new MockeryTestFoo2);
        $m->shouldReceive('bar')->andReturn('baz');
        $this->assertEquals('baz', $m->bar());
        $this->assertEquals('foo', $m->foo());
        $this->assertTrue($m instanceof MockeryTestFoo2);
    }
    
    public function testPassingClosureAsFinalParameterUsedToDefineExpectations()
    {
        $m = $this->container->mock('foo', function($m) {
            $m->shouldReceive('foo')->once()->andReturn('bar');
        });
        $this->assertEquals('bar', $m->foo());
    }
    
    /**
     * @expectedException \Mockery\Exception
     */
    public function testMockingAKnownConcreteFinalClassThrowsErrors_OnlyPartialMocksCanMockFinalElements()
    {
        $m = $this->container->mock('MockeryFoo3');
    }
    
    /**
     * @expectedException \Mockery\Exception
     */
    public function testMockingAKnownConcreteClassWithFinalMethodsThrowsErrors_OnlyPartialMocksCanMockFinalElements()
    {
        $m = $this->container->mock('MockeryFoo4');
    }
    
    public function testFinalClassesCanBePartialMocks()
    {
        $m = $this->container->mock(new MockeryFoo3);
        $m->shouldReceive('foo')->andReturn('baz');
        $this->assertEquals('baz', $m->foo());
        $this->assertFalse($m instanceof MockeryFoo3);
    }
    
    public function testClassesWithFinalMethodsCanBePartialMocks()
    {
        $m = $this->container->mock(new MockeryFoo4);
        $m->shouldReceive('foo')->andReturn('baz');
        $this->assertEquals('baz', $m->foo());
        $this->assertEquals('bar', $m->bar());
        $this->assertFalse($m instanceof MockeryFoo4);
    }
    
    public function testCanMockInterface()
    {
        $m = $this->container->mock('MockeryTest_Interface');
        $this->assertTrue($m instanceof MockeryTest_Interface);
    }
    
    public function testCanMockSpl()
    {
        $m = $this->container->mock('\\splFileObject');
        $this->assertTrue($m instanceof \splFileObject);
    }
    
    public function testCanMockInterfaceWithAbstractMethod()
    {
        $m = $this->container->mock('MockeryTest_InterfaceWithAbstractMethod');
        $this->assertTrue($m instanceof MockeryTest_InterfaceWithAbstractMethod);
        $m->shouldReceive('foo')->andReturn(1);
        $this->assertEquals(1, $m->foo());
    }
    
    public function testCanMockAbstractWithAbstractProtectedMethod()
    {
        $m = $this->container->mock('MockeryTest_AbstractWithAbstractMethod');
        $this->assertTrue($m instanceof MockeryTest_AbstractWithAbstractMethod);
    }
    
    public function testCanMockClassWithConstructor()
    {
        $m = $this->container->mock('MockeryTest_ClassConstructor');
        $this->assertTrue($m instanceof MockeryTest_ClassConstructor);
    }
    
    public function testCanMockClassWithConstructorNeedingClassArgs()
    {
        $m = $this->container->mock('MockeryTest_ClassConstructor2');
        $this->assertTrue($m instanceof MockeryTest_ClassConstructor2);
    }
    
    /**
     * @group issue/4
     */
    public function testCanMockClassContainingMagicCallMethod()
    {
        $m = $this->container->mock('MockeryTest_Call1');
        $this->assertTrue($m instanceof MockeryTest_Call1);
    }
    
    /**
     * @group issue/4
     */
    public function testCanMockClassContainingMagicCallMethodWithoutTypeHinting()
    {
        $m = $this->container->mock('MockeryTest_Call2');
        $this->assertTrue($m instanceof MockeryTest_Call2);
    }
    
}

class MockeryTestFoo {
    public function foo() { return 'foo'; }
}

class MockeryTestFoo2 {
    public function foo() { return 'foo'; }
    public function bar() { return 'bar'; }
}

final class MockeryFoo3 {
    public function foo() { return 'baz'; }
}

class MockeryFoo4 {
    final public function foo() { return 'baz'; }
    public function bar() { return 'bar'; }
}

interface MockeryTest_Interface {}

interface MockeryTest_InterfaceWithAbstractMethod
{
    public function set();
}

abstract class MockeryTest_AbstractWithAbstractMethod
{
    abstract protected function set();
}

class MockeryTest_ClassConstructor {
    public function __construct($param1) {}
}

class MockeryTest_ClassConstructor2 {
    public function __construct(stdClass $param1) {}
}

class MockeryTest_Call1 {
    public function __call($method, array $params) {}
}

class MockeryTest_Call2 {
    public function __call($method, $params) {}
}
