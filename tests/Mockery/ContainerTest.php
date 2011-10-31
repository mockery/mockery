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

    public function testBlockForwardingToPartialObject()
    {
        $m = $this->container->mock(new MockeryTestBar1, array('foo'=>1, \Mockery\Container::BLOCKS => array('method1')));
        $this->assertSame($m, $m->method1());
    }

    public function testPartialWithArrayDefs()
    {
        $m = $this->container->mock(new MockeryTestBar1, array('foo'=>1, \Mockery\Container::BLOCKS => array('method1')));
        $this->assertEquals(1, $m->foo());
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

    /**
     * @group finalclass
     */
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
     * @group partial
     */
    public function testCanPartiallyMockANormalClass()
    {
        $m = $this->container->mock('MockeryTest_PartialNormalClass[foo]');
        $this->assertTrue($m instanceof MockeryTest_PartialNormalClass);
        $m->shouldReceive('foo')->andReturn('cba');
        $this->assertEquals('abc', $m->bar());
        $this->assertEquals('cba', $m->foo());
    }

    /**
     * @group partial
     */
    public function testCanPartiallyMockAnAbstractClass()
    {
        $m = $this->container->mock('MockeryTest_PartialAbstractClass[foo]');
        $this->assertTrue($m instanceof MockeryTest_PartialAbstractClass);
        $m->shouldReceive('foo')->andReturn('cba');
        $this->assertEquals('abc', $m->bar());
        $this->assertEquals('cba', $m->foo());
    }

    /**
     * @group partial
     */
    public function testCanPartiallyMockANormalClassWith2Methods()
    {
        $m = $this->container->mock('MockeryTest_PartialNormalClass2[foo, baz]');
        $this->assertTrue($m instanceof MockeryTest_PartialNormalClass2);
        $m->shouldReceive('foo')->andReturn('cba');
        $m->shouldReceive('baz')->andReturn('cba');
        $this->assertEquals('abc', $m->bar());
        $this->assertEquals('cba', $m->foo());
        $this->assertEquals('cba', $m->baz());
    }

    /**
     * @group partial
     */
    public function testCanPartiallyMockAnAbstractClassWith2Methods()
    {
        $m = $this->container->mock('MockeryTest_PartialAbstractClass2[foo,baz]');
        $this->assertTrue($m instanceof MockeryTest_PartialAbstractClass2);
        $m->shouldReceive('foo')->andReturn('cba');
        $m->shouldReceive('baz')->andReturn('cba');
        $this->assertEquals('abc', $m->bar());
        $this->assertEquals('cba', $m->foo());
        $this->assertEquals('cba', $m->baz());
    }

    /**
     * @expectedException \Mockery\Exception
     * @group partial
     */
    public function testThrowsExceptionIfSettingExpectationForNonMockedMethodOfPartialMock()
    {
        $this->markTestSkipped('For now...');
        $m = $this->container->mock('MockeryTest_PartialNormalClass[foo]');
        $this->assertTrue($m instanceof MockeryTest_PartialNormalClass);
        $m->shouldReceive('bar')->andReturn('cba');
    }

    /**
     * @expectedException \Mockery\Exception
     * @group partial
     */
    public function testThrowsExceptionIfClassOrInterfaceForPartialMockDoesNotExist()
    {
        $m = $this->container->mock('MockeryTest_PartialNormalClassXYZ[foo]');
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

    /**
     * @group issue/14
     */
    public function testCanMockClassContainingAPublicWakeupMethod()
    {
        $m = $this->container->mock('MockeryTest_Wakeup1');
        $this->assertTrue($m instanceof MockeryTest_Wakeup1);
    }

    /**
     * @group issue/18
     */
    public function testCanMockClassUsingMagicCallMethodsInPlaceOfNormalMethods()
    {
        $m = \Mockery::mock('Gateway');
        $m->shouldReceive('iDoSomethingReallyCoolHere');
        $m->iDoSomethingReallyCoolHere();
    }

    /**
     * @group issue/18
     */
    public function testCanPartialMockObjectUsingMagicCallMethodsInPlaceOfNormalMethods()
    {
        $m = \Mockery::mock(new Gateway);
        $m->shouldReceive('iDoSomethingReallyCoolHere');
        $m->iDoSomethingReallyCoolHere();
    }

    /**
     * @group issue/13
     */
    public function testCanMockClassWhereMethodHasReferencedParameter()
    {
        $m = \Mockery::mock(new MockeryTest_MethodParamRef);
    }

    /**
     * @group issue/13
     */
    public function testCanPartiallyMockObjectWhereMethodHasReferencedParameter()
    {
        $m = \Mockery::mock(new MockeryTest_MethodParamRef2);
    }

    /**
     * @group issue/11
     */
    public function testMockingAKnownConcreteClassCanBeGrantedAnArbitraryClassType()
    {
        $m = $this->container->mock('alias:MyNamespace\MyClass');
        $m->shouldReceive('foo')->andReturn('bar');
        $this->assertEquals('bar', $m->foo());
        $this->assertTrue($m instanceof MyNamespace\MyClass);
    }

    /**
     * @group issue/15
     */
    public function testCanMockMultipleInterfaces()
    {
        $m = $this->container->mock('MockeryTest_Interface1, MockeryTest_Interface2');
        $this->assertTrue($m instanceof MockeryTest_Interface1);
        $this->assertTrue($m instanceof MockeryTest_Interface2);
    }

    /**
     * @expectedException \Mockery\Exception
     */
    public function testMockingMultipleInterfacesThrowsExceptionWhenGivenNonExistingClassOrInterface()
    {
        $m = $this->container->mock('DoesNotExist, MockeryTest_Interface2');
        $this->assertTrue($m instanceof MockeryTest_Interface1);
        $this->assertTrue($m instanceof MockeryTest_Interface2);
    }

    /**
     * @group issue/15
     */
    public function testCanMockClassAndApplyMultipleInterfaces()
    {
        $m = $this->container->mock('MockeryTestFoo, MockeryTest_Interface1, MockeryTest_Interface2');
        $this->assertTrue($m instanceof MockeryTestFoo);
        $this->assertTrue($m instanceof MockeryTest_Interface1);
        $this->assertTrue($m instanceof MockeryTest_Interface2);
    }

    /**
     * @group issue/7
     *
     * Noted: We could complicate internally, but a blind class is better built
     * with a real class noted up front (stdClass is a perfect choice it is
     * behaviourless). Fine, it's a muddle - but we need to draw a line somewhere.
     */
    public function testCanMockStaticMethods()
    {
        \Mockery::setContainer($this->container);
        $m = $this->container->mock('alias:MyNamespace\MyClass2');
        $m->shouldReceive('staticFoo')->andReturn('bar');
        $this->assertEquals('bar', \MyNameSpace\MyClass2::staticFoo());
        \Mockery::resetContainer();
    }

    /**
     * @group issue/7
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testMockedStaticMethodsObeyMethodCounting()
    {
        \Mockery::setContainer($this->container);
        $m = $this->container->mock('alias:MyNamespace\MyClass3');
        $m->shouldReceive('staticFoo')->once()->andReturn('bar');
        $this->container->mockery_verify();
        \Mockery::resetContainer();
    }

    /**
     * @group issue/17
     */
    public function testMockingAllowsPublicPropertyStubbingOnRealClass()
    {
        $m = $this->container->mock('MockeryTestFoo');
        $m->foo = 'bar';
        $this->assertEquals('bar', $m->foo);
        //$this->assertTrue(array_key_exists('foo', $m->mockery_getMockableProperties()));
    }

    /**
     * @group issue/17
     */
    public function testMockingAllowsPublicPropertyStubbingOnNamedMock()
    {
        $m = $this->container->mock('Foo');
        $m->foo = 'bar';
        $this->assertEquals('bar', $m->foo);
        //$this->assertTrue(array_key_exists('foo', $m->mockery_getMockableProperties()));
    }

    /**
     * @group issue/17
     */
    public function testMockingAllowsPublicPropertyStubbingOnPartials()
    {
        $m = $this->container->mock(new stdClass);
        $m->foo = 'bar';
        $this->assertEquals('bar', $m->foo);
        //$this->assertTrue(array_key_exists('foo', $m->mockery_getMockableProperties()));
    }

    /**
     * @group issue/17
     */
    public function testMockingDoesNotStubNonStubbedPropertiesOnPartials()
    {
        $m = $this->container->mock(new MockeryTest_ExistingProperty);
        $this->assertEquals('bar', $m->foo);
        $this->assertFalse(array_key_exists('foo', $m->mockery_getMockableProperties()));
    }

    public function testCreationOfInstanceMock()
    {
        $m = $this->container->mock('overload:MyNamespace\MyClass4');
        $this->assertTrue($m instanceof \MyNamespace\MyClass4);
    }

    public function testInstantiationOfInstanceMock()
    {
        \Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass5');
        $instance = new \MyNamespace\MyClass5;
        $this->assertTrue($instance instanceof \MyNamespace\MyClass5);
        \Mockery::resetContainer();
    }

    public function testInstantiationOfInstanceMockImportsExpectations()
    {
        \Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass6');
        $m->shouldReceive('foo')->andReturn('bar');
        $instance = new \MyNamespace\MyClass6;
        $this->assertEquals('bar', $instance->foo());
        \Mockery::resetContainer();
    }

    public function testInstantiationOfInstanceMocksIgnoresVerificationOfOriginMock()
    {
        \Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass7');
        $m->shouldReceive('foo')->once()->andReturn('bar');
        $this->container->mockery_verify();
        \Mockery::resetContainer(); //should not throw an exception
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testInstantiationOfInstanceMocksAddsThemToContainerForVerification()
    {
        \Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass8');
        $m->shouldReceive('foo')->once();
        $instance = new \MyNamespace\MyClass8;
        $this->container->mockery_verify();
        \Mockery::resetContainer();
    }

    public function testInstantiationOfInstanceMocksDoesNotHaveCountValidatorCrossover()
    {
        \Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass9');
        $m->shouldReceive('foo')->once();
        $instance1 = new \MyNamespace\MyClass9;
        $instance2 = new \MyNamespace\MyClass9;
        $instance1->foo();
        $instance2->foo();
        $this->container->mockery_verify();
        \Mockery::resetContainer();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testInstantiationOfInstanceMocksDoesNotHaveCountValidatorCrossover2()
    {
        \Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass10');
        $m->shouldReceive('foo')->once();
        $instance1 = new \MyNamespace\MyClass10;
        $instance2 = new \MyNamespace\MyClass10;
        $instance1->foo();
        $this->container->mockery_verify();
        \Mockery::resetContainer();
    }

    public function testMethodParamsPassedByReferenceHaveReferencePreserved()
    {
        $m = $this->container->mock('MockeryTestRef1');
        $m->shouldReceive('foo')->with(
            \Mockery::on(function(&$a) {$a += 1;return true;}),
            \Mockery::any()
        );
        $a = 1;
        $b = 1;
        $m->foo($a, $b);
        $this->assertEquals(2, $a);
        $this->assertEquals(1, $b);
    }

    public function testCanOverrideExpectedParametersOfInternalPHPClassesToPreserveRefs()
    {
        if (!class_exists('MongoCollection', false)) $this->markTestSkipped('ext/mongo not installed');
        \Mockery::getConfiguration()->setInternalClassMethodParamMap(
            'MongoCollection', 'insert', array('&$data', '$options')
        );
        $m = $this->container->mock('MongoCollection');
        $m->shouldReceive('insert')->with(
            \Mockery::on(function(&$data) {$data['_id'] = 123; return true;}),
            \Mockery::type('array')
        );
        $data = array('a'=>1,'b'=>2);
        $m->insert($data, array());
        $this->assertTrue(isset($data['_id']));
        $this->assertEquals(123, $data['_id']);
        $this->container->mockery_verify();
        \Mockery::resetContainer();
    }

    /**
     * @group abstract
     */
    public function testCanMockAbstractClassWithAbstractPublicMethod()
    {
        $m = $this->container->mock('MockeryTest_AbstractWithAbstractPublicMethod');
        $this->assertTrue($m instanceof MockeryTest_AbstractWithAbstractPublicMethod);
    }

    /**
     * @issue issue/21
     */
    public function testClassDeclaringIssetDoesNotThrowException()
    {
        \Mockery::setContainer($this->container);
        $m = $this->container->mock('MockeryTest_IssetMethod');
        $this->container->mockery_verify();
        \Mockery::resetContainer();
    }

    /**
     * @issue issue/21
     */
    public function testClassDeclaringUnsetDoesNotThrowException()
    {
        \Mockery::setContainer($this->container);
        $m = $this->container->mock('MockeryTest_UnsetMethod');
        $this->container->mockery_verify();
        \Mockery::resetContainer();
    }

}

class MockeryTest_IssetMethod
{
    protected $_properties = array();
    public function __construct() {}
    public function __isset($property=null) {
        return isset($this->_properties[$property]);
    }
}

class MockeryTest_UnsetMethod
{
    protected $_properties = array();
    public function __construct() {}
    public function __unset($property) {
        unset($this->_properties[$property]);
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
interface MockeryTest_Interface1 {}
interface MockeryTest_Interface2 {}

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

class MockeryTest_Wakeup1 {
    public function __construct() {}
    public function __wakeup() {}
}

class MockeryTest_ExistingProperty {
    public $foo = 'bar';
}
abstract class MockeryTest_AbstractWithAbstractPublicMethod{
    abstract public function foo($a, $b);
}

// issue/18
class SoCool
{
    public function iDoSomethingReallyCoolHere()
    {
        return 3;
    }
}
class Gateway
{
    public function __call($method, $args)
    {
        $m = new SoCool();
        return call_user_func_array(array($m, $method), $args);
    }
}

class MockeryTestBar1 {
    public function method1()
    {
        return $this;
    }
}

class MockeryTest_MethodParamRef {
    public function method1(&$foo){return true;}
}
class MockeryTest_MethodParamRef2 {
    public function method1(&$foo){return true;}
}
class MockeryTestRef1 {
    public function foo(&$a, $b) {}
}

class MockeryTest_PartialNormalClass {
    public function foo() {return 'abc';}
    public function bar() {return 'abc';}
}

abstract class MockeryTest_PartialAbstractClass {
    abstract public function foo();
    public function bar() {return 'abc';}
}

class MockeryTest_PartialNormalClass2 {
    public function foo() {return 'abc';}
    public function bar() {return 'abc';}
    public function baz() {return 'abc';}
}

abstract class MockeryTest_PartialAbstractClass2 {
    abstract public function foo();
    public function bar() {return 'abc';}
    abstract public function baz();
}
