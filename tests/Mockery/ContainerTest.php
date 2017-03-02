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

use Mockery\Generator\MockConfigurationBuilder;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ContainerTest extends MockeryTestCase
{
    /** @var Mockery\Container */
    private $container;

    public function setup()
    {
        $this->container = new Mockery\Container(Mockery::getDefaultGenerator(), new Mockery\Loader\EvalLoader());
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

    public function testGetKeyOfDemeterMockShouldReturnKeyWhenMatchingMock()
    {
        $m = $this->container->mock();
        $m->shouldReceive('foo->bar');
        $this->assertRegExp(
            '/Mockery_(\d+)__demeter_foo/',
            $this->container->getKeyOfDemeterMockFor('foo')
        );
    }
    public function testGetKeyOfDemeterMockShouldReturnNullWhenNoMatchingMock()
    {
        $method = 'unknownMethod';
        $this->assertNull($this->container->getKeyOfDemeterMockFor($method));

        $m = $this->container->mock();
        $m->shouldReceive('method');
        $this->assertNull($this->container->getKeyOfDemeterMockFor($method));

        $m->shouldReceive('foo->bar');
        $this->assertNull($this->container->getKeyOfDemeterMockFor($method));
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
        $m = $this->container->mock(array('foo'=>1, 'bar'=>2));
        $this->assertEquals(1, $m->foo());
        $this->assertEquals(2, $m->bar());
    }

    public function testSimpleMockWithArrayDefsCanBeOverridden()
    {
        // eg. In shared test setup
        $m = $this->container->mock(array('foo' => 1, 'bar' => 2));

        // and then overridden in one test
        $m->shouldReceive('foo')->with('baz')->once()->andReturn(2);
        $m->shouldReceive('bar')->with('baz')->once()->andReturn(42);

        $this->assertEquals(2, $m->foo('baz'));
        $this->assertEquals(42, $m->bar('baz'));
    }

    public function testNamedMockWithArrayDefs()
    {
        $m = $this->container->mock('Foo', array('foo'=>1, 'bar'=>2));
        $this->assertEquals(1, $m->foo());
        $this->assertEquals(2, $m->bar());
        try {
            $m->f();
        } catch (BadMethodCallException $e) {
            $this->assertTrue((bool) preg_match("/Foo/", $e->getMessage()));
        }
    }

    public function testNamedMockWithArrayDefsCanBeOverridden()
    {
        // eg. In shared test setup
        $m = $this->container->mock('Foo', array('foo' => 1));

        // and then overridden in one test
        $m->shouldReceive('foo')->with('bar')->once()->andReturn(2);

        $this->assertEquals(2, $m->foo('bar'));

        try {
            $m->f();
        } catch (BadMethodCallException $e) {
            $this->assertTrue((bool) preg_match("/Foo/", $e->getMessage()));
        }
    }

    public function testNamedMockMultipleInterfaces()
    {
        $m = $this->container->mock('stdClass, ArrayAccess, Countable', array('foo'=>1, 'bar'=>2));
        $this->assertEquals(1, $m->foo());
        $this->assertEquals(2, $m->bar());
        try {
            $m->f();
        } catch (BadMethodCallException $e) {
            $this->assertTrue((bool) preg_match("/stdClass/", $e->getMessage()));
            $this->assertTrue((bool) preg_match("/ArrayAccess/", $e->getMessage()));
            $this->assertTrue((bool) preg_match("/Countable/", $e->getMessage()));
        }
    }

    public function testNamedMockWithConstructorArgs()
    {
        $m = $this->container->mock("MockeryTest_ClassConstructor2[foo]", array($param1 = new stdClass()));
        $m->shouldReceive("foo")->andReturn(123);
        $this->assertEquals(123, $m->foo());
        $this->assertEquals($param1, $m->getParam1());
    }

    public function testNamedMockWithConstructorArgsAndArrayDefs()
    {
        $m = $this->container->mock(
            "MockeryTest_ClassConstructor2[foo]",
            array($param1 = new stdClass()),
            array("foo" => 123)
        );
        $this->assertEquals(123, $m->foo());
        $this->assertEquals($param1, $m->getParam1());
    }

    public function testNamedMockWithConstructorArgsWithInternalCallToMockedMethod()
    {
        $m = $this->container->mock("MockeryTest_ClassConstructor2[foo]", array($param1 = new stdClass()));
        $m->shouldReceive("foo")->andReturn(123);
        $this->assertEquals(123, $m->bar());
    }

    public function testNamedMockWithConstructorArgsButNoQuickDefsShouldLeaveConstructorIntact()
    {
        $m = $this->container->mock("MockeryTest_ClassConstructor2", array($param1 = new stdClass()));
        $m->shouldDeferMissing();
        $this->assertEquals($param1, $m->getParam1());
    }

    public function testNamedMockWithShouldDeferMissing()
    {
        $m = $this->container->mock("MockeryTest_ClassConstructor2", array($param1 = new stdClass()));
        $m->shouldDeferMissing();
        $this->assertEquals('foo', $m->bar());
        $m->shouldReceive("bar")->andReturn(123);
        $this->assertEquals(123, $m->bar());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testNamedMockWithShouldDeferMissingThrowsIfNotAvailable()
    {
        $m = $this->container->mock("MockeryTest_ClassConstructor2", array($param1 = new stdClass()));
        $m->shouldDeferMissing();
        $m->foorbar123();
    }

    public function testMockingAKnownConcreteClassSoMockInheritsClassType()
    {
        $m = $this->container->mock('stdClass');
        $m->shouldReceive('foo')->andReturn('bar');
        $this->assertEquals('bar', $m->foo());
        $this->assertTrue($m instanceof stdClass);
    }

    public function testMockingAKnownUserClassSoMockInheritsClassType()
    {
        $m = $this->container->mock('MockeryTest_TestInheritedType');
        $this->assertTrue($m instanceof MockeryTest_TestInheritedType);
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
        $m = $this->container->mock(new MockeryTestBar1, array('foo'=>1, Mockery\Container::BLOCKS => array('method1')));
        $this->assertSame($m, $m->method1());
    }

    public function testPartialWithArrayDefs()
    {
        $m = $this->container->mock(new MockeryTestBar1, array('foo'=>1, Mockery\Container::BLOCKS => array('method1')));
        $this->assertEquals(1, $m->foo());
    }

    public function testPassingClosureAsFinalParameterUsedToDefineExpectations()
    {
        $m = $this->container->mock('foo', function ($m) {
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

    public function testMockingAKnownConcreteClassWithFinalMethodsThrowsNoException()
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

    public function testSplClassWithFinalMethodsCanBeMocked()
    {
        $m = $this->container->mock('SplFileInfo');
        $m->shouldReceive('foo')->andReturn('baz');
        $this->assertEquals('baz', $m->foo());
        $this->assertTrue($m instanceof SplFileInfo);
    }

    public function testSplClassWithFinalMethodsCanBeMockedMultipleTimes()
    {
        $this->container->mock('SplFileInfo');
        $m = $this->container->mock('SplFileInfo');
        $m->shouldReceive('foo')->andReturn('baz');
        $this->assertEquals('baz', $m->foo());
        $this->assertTrue($m instanceof SplFileInfo);
    }

    public function testClassesWithFinalMethodsCanBeProxyPartialMocks()
    {
        $m = $this->container->mock(new MockeryFoo4);
        $m->shouldReceive('foo')->andReturn('baz');
        $this->assertEquals('baz', $m->foo());
        $this->assertEquals('bar', $m->bar());
        $this->assertTrue($m instanceof MockeryFoo4);
    }

    public function testClassesWithFinalMethodsCanBeProperPartialMocks()
    {
        $m = $this->container->mock('MockeryFoo4[bar]');
        $m->shouldReceive('bar')->andReturn('baz');
        $this->assertEquals('baz', $m->foo());
        $this->assertEquals('baz', $m->bar());
        $this->assertTrue($m instanceof MockeryFoo4);
    }

    public function testClassesWithFinalMethodsCanBeProperPartialMocksButFinalMethodsNotPartialed()
    {
        $m = $this->container->mock('MockeryFoo4[foo]');
        $m->shouldReceive('foo')->andReturn('foo');
        $this->assertEquals('baz', $m->foo()); // partial expectation ignored - will fail callcount assertion
        $this->assertTrue($m instanceof MockeryFoo4);
    }

    public function testSplfileinfoClassMockPassesUserExpectations()
    {
        $file = $this->container->mock('SplFileInfo[getFilename,getPathname,getExtension,getMTime]', array(__FILE__));
        $file->shouldReceive('getFilename')->once()->andReturn('foo');
        $file->shouldReceive('getPathname')->once()->andReturn('path/to/foo');
        $file->shouldReceive('getExtension')->once()->andReturn('css');
        $file->shouldReceive('getMTime')->once()->andReturn(time());
    }

    public function testCanMockInterface()
    {
        $m = $this->container->mock('MockeryTest_Interface');
        $this->assertTrue($m instanceof MockeryTest_Interface);
    }

    public function testCanMockSpl()
    {
        $m = $this->container->mock('\\SplFixedArray');
        $this->assertTrue($m instanceof SplFixedArray);
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

    public function testCanMockInterfaceWithPublicStaticMethod()
    {
        $m = $this->container->mock('MockeryTest_InterfaceWithPublicStaticMethod');
        $this->assertTrue($m instanceof MockeryTest_InterfaceWithPublicStaticMethod);
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
        $m = Mockery::mock('Gateway');
        $m->shouldReceive('iDoSomethingReallyCoolHere');
        $m->iDoSomethingReallyCoolHere();
    }

    /**
     * @group issue/18
     */
    public function testCanPartialMockObjectUsingMagicCallMethodsInPlaceOfNormalMethods()
    {
        $m = Mockery::mock(new Gateway);
        $m->shouldReceive('iDoSomethingReallyCoolHere');
        $m->iDoSomethingReallyCoolHere();
    }

    /**
     * @group issue/13
     */
    public function testCanMockClassWhereMethodHasReferencedParameter()
    {
        $m = Mockery::mock(new MockeryTest_MethodParamRef);
    }

    /**
     * @group issue/13
     */
    public function testCanPartiallyMockObjectWhereMethodHasReferencedParameter()
    {
        $m = Mockery::mock(new MockeryTest_MethodParamRef2);
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
     */
    public function testCanMockMultipleInterfacesThatMayNotExist()
    {
        $m = $this->container->mock('NonExistingClass, MockeryTest_Interface1, MockeryTest_Interface2, \Some\Thing\That\Doesnt\Exist');
        $this->assertTrue($m instanceof MockeryTest_Interface1);
        $this->assertTrue($m instanceof MockeryTest_Interface2);
        $this->assertTrue($m instanceof \Some\Thing\That\Doesnt\Exist);
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
        Mockery::setContainer($this->container);
        $m = $this->container->mock('alias:MyNamespace\MyClass2');
        $m->shouldReceive('staticFoo')->andReturn('bar');
        $this->assertEquals('bar', \MyNameSpace\MyClass2::staticFoo());
        Mockery::resetContainer();
    }

    /**
     * @group issue/7
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testMockedStaticMethodsObeyMethodCounting()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('alias:MyNamespace\MyClass3');
        $m->shouldReceive('staticFoo')->once()->andReturn('bar');
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testMockedStaticThrowsExceptionWhenMethodDoesNotExist()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('alias:MyNamespace\StaticNoMethod');
        $this->assertEquals('bar', MyNameSpace\StaticNoMethod::staticFoo());
        Mockery::resetContainer();
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
        $this->assertTrue($m instanceof MyNamespace\MyClass4);
    }

    public function testInstantiationOfInstanceMock()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass5');
        $instance = new MyNamespace\MyClass5;
        $this->assertTrue($instance instanceof MyNamespace\MyClass5);
        Mockery::resetContainer();
    }

    public function testInstantiationOfInstanceMockImportsExpectations()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass6');
        $m->shouldReceive('foo')->andReturn('bar');
        $instance = new MyNamespace\MyClass6;
        $this->assertEquals('bar', $instance->foo());
        Mockery::resetContainer();
    }

    public function testInstantiationOfInstanceMockImportsDefaultExpectations()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass6');
        $m->shouldReceive('foo')->andReturn('bar')->byDefault();
        $instance = new MyNamespace\MyClass6;

        $this->assertEquals('bar', $instance->foo());

        Mockery::resetContainer();
    }

    public function testInstantiationOfInstanceMockImportsDefaultExpectationsInTheCorrectOrder()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass6');
        $m->shouldReceive('foo')->andReturn(1)->byDefault();
        $m->shouldReceive('foo')->andReturn(2)->byDefault();
        $m->shouldReceive('foo')->andReturn(3)->byDefault();
        $instance = new MyNamespace\MyClass6;

        $this->assertEquals(3, $instance->foo());

        Mockery::resetContainer();
    }

    public function testInstantiationOfInstanceMocksIgnoresVerificationOfOriginMock()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass7');
        $m->shouldReceive('foo')->once()->andReturn('bar');
        $this->container->mockery_verify();
        Mockery::resetContainer(); //should not throw an exception
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testInstantiationOfInstanceMocksAddsThemToContainerForVerification()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass8');
        $m->shouldReceive('foo')->once();
        $instance = new MyNamespace\MyClass8;
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    public function testInstantiationOfInstanceMocksDoesNotHaveCountValidatorCrossover()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass9');
        $m->shouldReceive('foo')->once();
        $instance1 = new MyNamespace\MyClass9;
        $instance2 = new MyNamespace\MyClass9;
        $instance1->foo();
        $instance2->foo();
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    /**
     * @expectedException \Mockery\CountValidator\Exception
     */
    public function testInstantiationOfInstanceMocksDoesNotHaveCountValidatorCrossover2()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass10');
        $m->shouldReceive('foo')->once();
        $instance1 = new MyNamespace\MyClass10;
        $instance2 = new MyNamespace\MyClass10;
        $instance1->foo();
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    public function testCreationOfInstanceMockWithFullyQualifiedName()
    {
        $m = $this->container->mock('overload:\MyNamespace\MyClass11');
        $this->assertTrue($m instanceof MyNamespace\MyClass11);
    }

    public function testInstanceMocksShouldIgnoreMissing()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('overload:MyNamespace\MyClass12');
        $m->shouldIgnoreMissing();

        $instance = new MyNamespace\MyClass12();
        $instance->foo();

        Mockery::resetContainer();
    }

    public function testMethodParamsPassedByReferenceHaveReferencePreserved()
    {
        $m = $this->container->mock('MockeryTestRef1');
        $m->shouldReceive('foo')->with(
            Mockery::on(function (&$a) {$a += 1;return true;}),
            Mockery::any()
        );
        $a = 1;
        $b = 1;
        $m->foo($a, $b);
        $this->assertEquals(2, $a);
        $this->assertEquals(1, $b);
    }

    public function testMethodParamsPassedByReferenceThroughWithArgsHaveReferencePreserved()
    {
        $m = $this->container->mock('MockeryTestRef1');
        $m->shouldReceive('foo')->withArgs(function (&$a, $b) {$a += 1; $b += 1; return true;});
        $a = 1;
        $b = 1;
        $m->foo($a, $b);
        $this->assertEquals(2, $a);
        $this->assertEquals(1, $b);
    }

    /**
     * Meant to test the same logic as
     * testCanOverrideExpectedParametersOfExtensionPHPClassesToPreserveRefs,
     * but:
     * - doesn't require an extension
     * - isn't actually known to be used
     */
    public function testCanOverrideExpectedParametersOfInternalPHPClassesToPreserveRefs()
    {
        Mockery::getConfiguration()->setInternalClassMethodParamMap(
            'DateTime', 'modify', array('&$string')
        );
        // @ used to avoid E_STRICT for incompatible signature
        @$m = $this->container->mock('DateTime');
        $this->assertInstanceOf("Mockery\MockInterface", $m, "Mocking failed, remove @ error suppresion to debug");
        $m->shouldReceive('modify')->with(
            Mockery::on(function (&$string) {$string = 'foo'; return true;})
        );
        $data ='bar';
        $m->modify($data);
        $this->assertEquals('foo', $data);
        $this->container->mockery_verify();
        Mockery::resetContainer();
        Mockery::getConfiguration()->resetInternalClassMethodParamMaps();
    }

    /**
     * Real world version of
     * testCanOverrideExpectedParametersOfInternalPHPClassesToPreserveRefs
     */
    public function testCanOverrideExpectedParametersOfExtensionPHPClassesToPreserveRefs()
    {
        if (!class_exists('MongoCollection', false)) {
            $this->markTestSkipped('ext/mongo not installed');
        }
        Mockery::getConfiguration()->setInternalClassMethodParamMap(
            'MongoCollection', 'insert', array('&$data', '$options')
        );
        // @ used to avoid E_STRICT for incompatible signature
        @$m = $this->container->mock('MongoCollection');
        $this->assertInstanceOf("Mockery\MockInterface", $m, "Mocking failed, remove @ error suppresion to debug");
        $m->shouldReceive('insert')->with(
            Mockery::on(function (&$data) {$data['_id'] = 123; return true;}),
            Mockery::type('array')
        );
        $data = array('a'=>1,'b'=>2);
        $m->insert($data, array());
        $this->assertTrue(isset($data['_id']));
        $this->assertEquals(123, $data['_id']);
        $this->container->mockery_verify();
        Mockery::resetContainer();
        Mockery::getConfiguration()->resetInternalClassMethodParamMaps();
    }

    public function testCanCreateNonOverridenInstanceOfPreviouslyOverridenInternalClasses()
    {
        Mockery::getConfiguration()->setInternalClassMethodParamMap(
            'DateTime', 'modify', array('&$string')
        );
        // @ used to avoid E_STRICT for incompatible signature
        @$m = $this->container->mock('DateTime');
        $this->assertInstanceOf("Mockery\MockInterface", $m, "Mocking failed, remove @ error suppresion to debug");
        $rc = new ReflectionClass($m);
        $rm = $rc->getMethod('modify');
        $params = $rm->getParameters();
        $this->assertTrue($params[0]->isPassedByReference());

        Mockery::getConfiguration()->resetInternalClassMethodParamMaps();

        $m = $this->container->mock('DateTime');
        $this->assertInstanceOf("Mockery\MockInterface", $m, "Mocking failed");
        $rc = new ReflectionClass($m);
        $rm = $rc->getMethod('modify');
        $params = $rm->getParameters();
        $this->assertFalse($params[0]->isPassedByReference());

        Mockery::resetContainer();
        Mockery::getConfiguration()->resetInternalClassMethodParamMaps();
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
        Mockery::setContainer($this->container);
        $m = $this->container->mock('MockeryTest_IssetMethod');
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    /**
     * @issue issue/21
     */
    public function testClassDeclaringUnsetDoesNotThrowException()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('MockeryTest_UnsetMethod');
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    /**
     * @issue issue/35
     */
    public function testCallingSelfOnlyReturnsLastMockCreatedOrCurrentMockBeingProgrammedSinceTheyAreOneAndTheSame()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('MockeryTestFoo');
        $this->assertFalse($this->container->self() instanceof MockeryTestFoo2);
        //$m = $this->container->mock('MockeryTestFoo2');
        //$this->assertTrue($this->container->self() instanceof MockeryTestFoo2);
        //$m = $this->container->mock('MockeryTestFoo');
        //$this->assertFalse(Mockery::self() instanceof MockeryTestFoo2);
        //$this->assertTrue(Mockery::self() instanceof MockeryTestFoo);
        Mockery::resetContainer();
    }

    /**
     * @issue issue/89
     */
    public function testCreatingMockOfClassWithExistingToStringMethodDoesntCreateClassWithTwoToStringMethods()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('MockeryTest_WithToString'); // this would fatal
        $m->shouldReceive("__toString")->andReturn('dave');
        $this->assertEquals("dave", "$m");
    }

    public function testGetExpectationCount_freshContainer()
    {
        $this->assertEquals(0, $this->container->mockery_getExpectationCount());
    }

    public function testGetExpectationCount_simplestMock()
    {
        $m = $this->container->mock();
        $m->shouldReceive('foo')->andReturn('bar');
        $this->assertEquals(1, $this->container->mockery_getExpectationCount());
    }

    public function testMethodsReturningParamsByReferenceDoesNotErrorOut()
    {
        $this->container->mock('MockeryTest_ReturnByRef');
        $mock = $this->container->mock('MockeryTest_ReturnByRef');
        $mock->shouldReceive("get")->andReturn($var = 123);
        $this->assertSame($var, $mock->get());
    }

    public function testMockCallableTypeHint()
    {
        if (PHP_VERSION_ID >= 50400) {
            $this->container->mock('MockeryTest_MockCallableTypeHint');
        }
    }

    public function testCanMockClassWithReservedWordMethod()
    {
        if (!extension_loaded("redis")) {
            $this->markTestSkipped("phpredis not installed");
        }

        $this->container->mock("Redis");
    }

    public function testUndeclaredClassIsDeclared()
    {
        $this->assertFalse(class_exists("BlahBlah"));
        $mock = $this->container->mock("BlahBlah");
        $this->assertInstanceOf("BlahBlah", $mock);
    }

    public function testUndeclaredClassWithNamespaceIsDeclared()
    {
        $this->assertFalse(class_exists("MyClasses\Blah\BlahBlah"));
        $mock = $this->container->mock("MyClasses\Blah\BlahBlah");
        $this->assertInstanceOf("MyClasses\Blah\BlahBlah", $mock);
    }

    public function testUndeclaredClassWithNamespaceIncludingLeadingOperatorIsDeclared()
    {
        $this->assertFalse(class_exists("\MyClasses\DaveBlah\BlahBlah"));
        $mock = $this->container->mock("\MyClasses\DaveBlah\BlahBlah");
        $this->assertInstanceOf("\MyClasses\DaveBlah\BlahBlah", $mock);
    }

    public function testMockingPhpredisExtensionClassWorks()
    {
        if (!class_exists('Redis')) {
            $this->markTestSkipped('PHPRedis extension required for this test');
        }
        $m = $this->container->mock('Redis');
    }

    public function testIssetMappingUsingProxiedPartials_CheckNoExceptionThrown()
    {
        $var = $this->container->mock(new MockeryTestIsset_Bar());
        $mock = $this->container->mock(new MockeryTestIsset_Foo($var));
        $mock->shouldReceive('bar')->once();
        $mock->bar();
        $this->container->mockery_teardown(); // closed by teardown()
    }

    /**
     * @group traversable1
     */
    public function testCanMockInterfacesExtendingTraversable()
    {
        $mock = $this->container->mock('MockeryTest_InterfaceWithTraversable');
        $this->assertInstanceOf('MockeryTest_InterfaceWithTraversable', $mock);
        $this->assertInstanceOf('ArrayAccess', $mock);
        $this->assertInstanceOf('Countable', $mock);
        $this->assertInstanceOf('Traversable', $mock);
    }

    /**
     * @group traversable2
     */
    public function testCanMockInterfacesAlongsideTraversable()
    {
        $mock = $this->container->mock('stdClass, ArrayAccess, Countable, Traversable');
        $this->assertInstanceOf('stdClass', $mock);
        $this->assertInstanceOf('ArrayAccess', $mock);
        $this->assertInstanceOf('Countable', $mock);
        $this->assertInstanceOf('Traversable', $mock);
    }

    public function testInterfacesCanHaveAssertions()
    {
        Mockery::setContainer($this->container);
        $m = $this->container->mock('stdClass, ArrayAccess, Countable, Traversable');
        $m->shouldReceive('foo')->once();
        $m->foo();
        $this->container->mockery_verify();
        Mockery::resetContainer();
    }

    public function testMockingIteratorAggregateDoesNotImplementIterator()
    {
        $mock = $this->container->mock('MockeryTest_ImplementsIteratorAggregate');
        $this->assertInstanceOf('IteratorAggregate', $mock);
        $this->assertInstanceOf('Traversable', $mock);
        $this->assertNotInstanceOf('Iterator', $mock);
    }

    public function testMockingInterfaceThatExtendsIteratorDoesNotImplementIterator()
    {
        $mock = $this->container->mock('MockeryTest_InterfaceThatExtendsIterator');
        $this->assertInstanceOf('Iterator', $mock);
        $this->assertInstanceOf('Traversable', $mock);
    }

    public function testMockingInterfaceThatExtendsIteratorAggregateDoesNotImplementIterator()
    {
        $mock = $this->container->mock('MockeryTest_InterfaceThatExtendsIteratorAggregate');
        $this->assertInstanceOf('IteratorAggregate', $mock);
        $this->assertInstanceOf('Traversable', $mock);
        $this->assertNotInstanceOf('Iterator', $mock);
    }

    public function testMockingIteratorAggregateDoesNotImplementIteratorAlongside()
    {
        $mock = $this->container->mock('IteratorAggregate');
        $this->assertInstanceOf('IteratorAggregate', $mock);
        $this->assertInstanceOf('Traversable', $mock);
        $this->assertNotInstanceOf('Iterator', $mock);
    }

    public function testMockingIteratorDoesNotImplementIteratorAlongside()
    {
        $mock = $this->container->mock('Iterator');
        $this->assertInstanceOf('Iterator', $mock);
        $this->assertInstanceOf('Traversable', $mock);
    }

    public function testMockingIteratorDoesNotImplementIterator()
    {
        $mock = $this->container->mock('MockeryTest_ImplementsIterator');
        $this->assertInstanceOf('Iterator', $mock);
        $this->assertInstanceOf('Traversable', $mock);
    }

    public function testMockeryCloseForIllegalIssetFileInclude()
    {
        $m = Mockery::mock('StdClass')
            ->shouldReceive('get')
            ->andReturn(false)
            ->getMock();
        $m->get();
        Mockery::close();
    }

    public function testMockeryShouldDistinguishBetweenConstructorParamsAndClosures()
    {
        $obj = new MockeryTestFoo();
        $mock = $this->container->mock('MockeryTest_ClassMultipleConstructorParams[dave]',
            array( &$obj, 'foo' ));
    }

    /** @group nette */
    public function testMockeryShouldNotMockCallstaticMagicMethod()
    {
        $mock = $this->container->mock('MockeryTest_CallStatic');
    }

    /**
     * @issue issue/139
     */
    public function testCanMockClassWithOldStyleConstructorAndArguments()
    {
        $mock = $this->container->mock('MockeryTest_OldStyleConstructor');
    }

    /** @group issue/144 */
    public function testMockeryShouldInterpretEmptyArrayAsConstructorArgs()
    {
        $mock = $this->container->mock("EmptyConstructorTest", array());
        $this->assertSame(0, $mock->numberOfConstructorArgs);
    }

    /** @group issue/144 */
    public function testMockeryShouldCallConstructorByDefaultWhenRequestingPartials()
    {
        $mock = $this->container->mock("EmptyConstructorTest[foo]");
        $this->assertSame(0, $mock->numberOfConstructorArgs);
    }

    /** @group issue/158 */
    public function testMockeryShouldRespectInterfaceWithMethodParamSelf()
    {
        $this->container->mock('MockeryTest_InterfaceWithMethodParamSelf');
    }

    /** @group issue/162 */
    public function testMockeryDoesntTryAndMockLowercaseToString()
    {
        $this->container->mock('MockeryTest_Lowercase_ToString');
    }

    /** @group issue/175 */
    public function testExistingStaticMethodMocking()
    {
        Mockery::setContainer($this->container);
        $mock = $this->container->mock('MockeryTest_PartialStatic[mockMe]');

        $mock->shouldReceive('mockMe')->with(5)->andReturn(10);

        $this->assertEquals(10, $mock::mockMe(5));
        $this->assertEquals(3, $mock::keepMe(3));
    }

    /**
     * @group issue/154
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage protectedMethod() cannot be mocked as it is a protected method and mocking protected methods is not enabled for the currently used mock object.
     */
    public function testShouldThrowIfAttemptingToStubProtectedMethod()
    {
        $mock = $this->container->mock('MockeryTest_WithProtectedAndPrivate');
        $mock->shouldReceive("protectedMethod");
    }

    /**
     * @group issue/154
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage privateMethod() cannot be mocked as it is a private method
     */
    public function testShouldThrowIfAttemptingToStubPrivateMethod()
    {
        $mock = $this->container->mock('MockeryTest_WithProtectedAndPrivate');
        $mock->shouldReceive("privateMethod");
    }

    public function testWakeupMagicIsNotMockedToAllowSerialisationInstanceHack()
    {
        $mock = $this->container->mock('DateTime');
    }

    /**
     * @group issue/154
     */
    public function testCanMockMethodsWithRequiredParamsThatHaveDefaultValues()
    {
        $mock = $this->container->mock('MockeryTest_MethodWithRequiredParamWithDefaultValue');
        $mock->shouldIgnoreMissing();
        $mock->foo(null, 123);
    }

    /**
     * @test
     * @group issue/294
     * @expectedException Mockery\Exception\RuntimeException
     * @expectedExceptionMessage Could not load mock DateTime, class already exists
     */
    public function testThrowsWhenNamedMockClassExistsAndIsNotMockery()
    {
        $builder = new MockConfigurationBuilder();
        $builder->setName("DateTime");
        $mock = $this->container->mock($builder);
    }

    /**
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     * @expectedExceptionMessage MyTestClass::foo(resource(...))
     */
    public function testHandlesMethodWithArgumentExpectationWhenCalledWithResource()
    {
        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive('foo')->with(array('yourself' => 21));

        $mock->foo(fopen('php://memory', 'r'));
    }

    /**
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     * @expectedExceptionMessage MyTestClass::foo(['myself' => [...]])
     */
    public function testHandlesMethodWithArgumentExpectationWhenCalledWithCircularArray()
    {
        $testArray = array();
        $testArray['myself'] =& $testArray;

        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive('foo')->with(array('yourself' => 21));

        $mock->foo($testArray);
    }

    /**
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     * @expectedExceptionMessage MyTestClass::foo(['a_scalar' => 2, 'an_array' => [...]])
     */
    public function testHandlesMethodWithArgumentExpectationWhenCalledWithNestedArray()
    {
        $testArray = array();
        $testArray['a_scalar'] = 2;
        $testArray['an_array'] = array(1, 2, 3);

        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive('foo')->with(array('yourself' => 21));

        $mock->foo($testArray);
    }

    /**
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     * @expectedExceptionMessage MyTestClass::foo(['a_scalar' => 2, 'an_object' => object(stdClass)])
     */
    public function testHandlesMethodWithArgumentExpectationWhenCalledWithNestedObject()
    {
        $testArray = array();
        $testArray['a_scalar'] = 2;
        $testArray['an_object'] = new stdClass();

        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive('foo')->with(array('yourself' => 21));

        $mock->foo($testArray);
    }

    /**
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     * @expectedExceptionMessage MyTestClass::foo(['a_scalar' => 2, 'a_closure' => object(Closure
     */
    public function testHandlesMethodWithArgumentExpectationWhenCalledWithNestedClosure()
    {
        $testArray = array();
        $testArray['a_scalar'] = 2;
        $testArray['a_closure'] = function () {
        };

        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive('foo')->with(array('yourself' => 21));

        $mock->foo($testArray);
    }

    /**
     * @expectedException Mockery\Exception\NoMatchingExpectationException
     * @expectedExceptionMessage MyTestClass::foo(['a_scalar' => 2, 'a_resource' => resource(...)])
     */
    public function testHandlesMethodWithArgumentExpectationWhenCalledWithNestedResource()
    {
        $testArray = array();
        $testArray['a_scalar'] = 2;
        $testArray['a_resource'] = fopen('php://memory', 'r');

        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive('foo')->with(array('yourself' => 21));

        $mock->foo($testArray);
    }

    public function testExceptionOutputMakesBooleansLookLikeBooleans()
    {
        $mock = $this->container->mock('MyTestClass');
        $mock->shouldReceive("foo")->with(123);

        $this->setExpectedException(
            "Mockery\Exception\NoMatchingExpectationException",
            "MyTestClass::foo(true, false, [0 => true, 1 => false])"
        );

        $mock->foo(true, false, [true, false]);
    }

    /**
     * @test
     * @group issue/339
     */
    public function canMockClassesThatDescendFromInternalClasses()
    {
        $mock = $this->container->mock("MockeryTest_ClassThatDescendsFromInternalClass");
        $this->assertInstanceOf("DateTime", $mock);
    }

    /**
     * @test
     * @group issue/339
     */
    public function canMockClassesThatImplementSerializable()
    {
        $mock = $this->container->mock("MockeryTest_ClassThatImplementsSerializable");
        $this->assertInstanceOf("Serializable", $mock);
    }

    /**
     * @test
     * @group issue/346
     */
    public function canMockInternalClassesThatImplementSerializable()
    {
        $mock = $this->container->mock("ArrayObject");
        $this->assertInstanceOf("Serializable", $mock);
    }

    /**
     * @dataProvider classNameProvider
     */
    public function testIsValidClassName($expected, $className)
    {
        $container = new \Mockery\Container;
        $this->assertSame($expected, $container->isValidClassName($className));
    }

    public function classNameProvider()
    {
        return array(
            array(false, ' '), // just a space
            array(false, 'ClassName.WithDot'),
            array(false, '\\\\TooManyBackSlashes'),
            array(true,  'Foo'),
            array(true,  '\\Foo\\Bar'),
        );
    }
}

class MockeryTest_CallStatic
{
    public static function __callStatic($method, $args)
    {
    }
}

class MockeryTest_ClassMultipleConstructorParams
{
    public function __construct($a, $b)
    {
    }

    public function dave()
    {
    }
}

interface MockeryTest_InterfaceWithTraversable extends ArrayAccess, Traversable, Countable
{
    public function self();
}

class MockeryTestIsset_Bar
{
    public function doSomething()
    {
    }
}

class MockeryTestIsset_Foo
{
    private $var;

    public function __construct($var)
    {
        $this->var = $var;
    }

    public function __get($name)
    {
        $this->var->doSomething();
    }

    public function __isset($name)
    {
        return (bool) strlen($this->__get($name));
    }
}

class MockeryTest_IssetMethod
{
    protected $_properties = array();

    public function __construct()
    {
    }

    public function __isset($property)
    {
        return isset($this->_properties[$property]);
    }
}

class MockeryTest_UnsetMethod
{
    protected $_properties = array();

    public function __construct()
    {
    }

    public function __unset($property)
    {
        unset($this->_properties[$property]);
    }
}

class MockeryTestFoo
{
    public function foo()
    {
        return 'foo';
    }
}

class MockeryTestFoo2
{
    public function foo()
    {
        return 'foo';
    }

    public function bar()
    {
        return 'bar';
    }
}

final class MockeryFoo3
{
    public function foo()
    {
        return 'baz';
    }
}

class MockeryFoo4
{
    final public function foo()
    {
        return 'baz';
    }

    public function bar()
    {
        return 'bar';
    }
}

interface MockeryTest_Interface
{
}
interface MockeryTest_Interface1
{
}
interface MockeryTest_Interface2
{
}

interface MockeryTest_InterfaceWithAbstractMethod
{
    public function set();
}

interface MockeryTest_InterfaceWithPublicStaticMethod
{
    public static function self();
}

abstract class MockeryTest_AbstractWithAbstractMethod
{
    abstract protected function set();
}

class MockeryTest_WithProtectedAndPrivate
{
    protected function protectedMethod()
    {
    }

    private function privateMethod()
    {
    }
}

class MockeryTest_ClassConstructor
{
    public function __construct($param1)
    {
    }
}

class MockeryTest_ClassConstructor2
{
    protected $param1;

    public function __construct(stdClass $param1)
    {
        $this->param1 = $param1;
    }

    public function getParam1()
    {
        return $this->param1;
    }

    public function foo()
    {
        return 'foo';
    }

    public function bar()
    {
        return $this->foo();
    }
}

class MockeryTest_Call1
{
    public function __call($method, array $params)
    {
    }
}

class MockeryTest_Call2
{
    public function __call($method, $params)
    {
    }
}

class MockeryTest_Wakeup1
{
    public function __construct()
    {
    }

    public function __wakeup()
    {
    }
}

class MockeryTest_ExistingProperty
{
    public $foo = 'bar';
}

abstract class MockeryTest_AbstractWithAbstractPublicMethod
{
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

class MockeryTestBar1
{
    public function method1()
    {
        return $this;
    }
}

class MockeryTest_ReturnByRef
{
    public $i = 0;

    public function &get()
    {
        return $this->$i;
    }
}

class MockeryTest_MethodParamRef
{
    public function method1(&$foo)
    {
        return true;
    }
}
class MockeryTest_MethodParamRef2
{
    public function method1(&$foo)
    {
        return true;
    }
}
class MockeryTestRef1
{
    public function foo(&$a, $b)
    {
    }
}

class MockeryTest_PartialNormalClass
{
    public function foo()
    {
        return 'abc';
    }

    public function bar()
    {
        return 'abc';
    }
}

abstract class MockeryTest_PartialAbstractClass
{
    abstract public function foo();

    public function bar()
    {
        return 'abc';
    }
}

class MockeryTest_PartialNormalClass2
{
    public function foo()
    {
        return 'abc';
    }

    public function bar()
    {
        return 'abc';
    }

    public function baz()
    {
        return 'abc';
    }
}

abstract class MockeryTest_PartialAbstractClass2
{
    abstract public function foo();

    public function bar()
    {
        return 'abc';
    }

    abstract public function baz();
}

class MockeryTest_TestInheritedType
{
}

if (PHP_VERSION_ID >= 50400) {
    class MockeryTest_MockCallableTypeHint
    {
        public function foo(callable $baz)
        {
            $baz();
        }

        public function bar(callable $callback = null)
        {
            $callback();
        }
    }
}

class MockeryTest_WithToString
{
    public function __toString()
    {
    }
}

class MockeryTest_ImplementsIteratorAggregate implements IteratorAggregate
{
    public function getIterator()
    {
        return new ArrayIterator(array());
    }
}

class MockeryTest_ImplementsIterator implements Iterator
{
    public function rewind()
    {
    }

    public function current()
    {
    }

    public function key()
    {
    }

    public function next()
    {
    }

    public function valid()
    {
    }
}

class MockeryTest_OldStyleConstructor
{
    public function MockeryTest_OldStyleConstructor($arg)
    {
    }
}

class EmptyConstructorTest
{
    public $numberOfConstructorArgs;

    public function __construct(...$args)
    {
        $this->numberOfConstructorArgs = count($args);
    }

    public function foo()
    {
    }
}

interface MockeryTest_InterfaceWithMethodParamSelf
{
    public function foo(self $bar);
}

class MockeryTest_Lowercase_ToString
{
    public function __tostring()
    {
    }
}

class MockeryTest_PartialStatic
{
    public static function mockMe($a)
    {
        return $a;
    }

    public static function keepMe($b)
    {
        return $b;
    }
}

class MockeryTest_MethodWithRequiredParamWithDefaultValue
{
    public function foo(DateTime $bar = null, $baz)
    {
    }
}

interface MockeryTest_InterfaceThatExtendsIterator extends Iterator
{
    public function foo();
}

interface MockeryTest_InterfaceThatExtendsIteratorAggregate extends IteratorAggregate
{
    public function foo();
}

class MockeryTest_ClassThatDescendsFromInternalClass extends DateTime
{
}

class MockeryTest_ClassThatImplementsSerializable implements Serializable
{
    public function serialize()
    {
    }

    public function unserialize($serialized)
    {
    }
}
