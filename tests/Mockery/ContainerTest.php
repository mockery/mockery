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
    
}

class MockeryTestFoo {
    public function foo() { return 'foo'; }
}

class MockeryTestFoo2 {
    public function foo() { return 'foo'; }
    public function bar() { return 'bar'; }
}
