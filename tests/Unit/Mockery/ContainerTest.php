<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery;

use ArrayAccess;
use ArrayObject;
use Countable;
use DateTime;
use Exception;
use Foo\Bar;
use Generator;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
use LogicException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Container;
use Mockery\Exception\BadMethodCallException;
use Mockery\Exception\NoMatchingExpectationException;
use Mockery\Exception\RuntimeException;
use Mockery\Generator\MockConfigurationBuilder;
use Mockery\MockInterface;
use MongoCollection;
use MyNamespace\MyClass;
use MyNamespace\MyClass10;
use MyNamespace\MyClass11;
use MyNamespace\MyClass12;
use MyNamespace\MyClass13;
use MyNamespace\MyClass14;
use MyNamespace\MyClass15;
use MyNamespace\MyClass16;
use MyNameSpace\MyClass2;
use MyNamespace\MyClass4;
use MyNamespace\MyClass5;
use MyNamespace\MyClass6;
use MyNamespace\MyClass8;
use MyNamespace\MyClass9;
use MyNameSpace\StaticNoMethod;
use PHP73\EmptyConstructorTest;
use PHP73\Gateway;
use PHP73\MockeryFoo3;
use PHP73\MockeryFoo4;
use PHP73\MockeryTest_AbstractWithAbstractMethod;
use PHP73\MockeryTest_AbstractWithAbstractPublicMethod;
use PHP73\MockeryTest_Call1;
use PHP73\MockeryTest_Call2;
use PHP73\MockeryTest_CallStatic;
use PHP73\MockeryTest_ClassConstructor;
use PHP73\MockeryTest_ClassConstructor2;
use PHP73\MockeryTest_ClassThatDescendsFromInternalClass;
use PHP73\MockeryTest_ExistingProperty;
use PHP73\MockeryTest_ImplementsIterator;
use PHP73\MockeryTest_ImplementsIteratorAggregate;
use PHP73\MockeryTest_Interface;
use PHP73\MockeryTest_Interface1;
use PHP73\MockeryTest_Interface2;
use PHP73\MockeryTest_InterfaceThatExtendsIterator;
use PHP73\MockeryTest_InterfaceThatExtendsIteratorAggregate;
use PHP73\MockeryTest_InterfaceWithAbstractMethod;
use PHP73\MockeryTest_InterfaceWithMethodParamSelf;
use PHP73\MockeryTest_InterfaceWithPublicStaticMethod;
use PHP73\MockeryTest_InterfaceWithTraversable;
use PHP73\MockeryTest_IssetMethod;
use PHP73\MockeryTest_Lowercase_ToString;
use PHP73\MockeryTest_MethodParamRef;
use PHP73\MockeryTest_MethodParamRef2;
use PHP73\MockeryTest_MethodWithRequiredParamWithDefaultValue;
use PHP73\MockeryTest_MockCallableTypeHint;
use PHP73\MockeryTest_PartialAbstractClass;
use PHP73\MockeryTest_PartialAbstractClass2;
use PHP73\MockeryTest_PartialNormalClass;
use PHP73\MockeryTest_PartialNormalClass2;
use PHP73\MockeryTest_ReturnByRef;
use PHP73\MockeryTest_TestInheritedType;
use PHP73\MockeryTest_UnsetMethod;
use PHP73\MockeryTest_Wakeup1;
use PHP73\MockeryTest_WithProtectedAndPrivate;
use PHP73\MockeryTest_WithToString;
use PHP73\MockeryTestBar1;
use PHP73\MockeryTestFoo;
use PHP73\MockeryTestFoo2;
use PHP73\MockeryTestIsset_Bar;
use PHP73\MockeryTestIsset_Foo;
use PHP73\MockeryTestRef1;
use PHP81\MockeryTest_ClassThatImplementsSerializable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;
use Redis;
use ReflectionClass;
use Serializable;
use Some\Thing\That\Doesnt\Exist;
use SplFileInfo;
use SplFixedArray;
use stdClass;
use Throwable;
use Traversable;

use const PHP_MAJOR_VERSION;

use function class_exists;
use function extension_loaded;
use function fopen;
use function get_class;
use function mock;
use function preg_match;
use function random_int;
use function time;
use function uniqid;

/**
 * @coversDefaultClass \Mockery
 */
final class ContainerTest extends MockeryTestCase
{
    /**
     * @return Generator<string,array{0: bool, 1: string}>
     */
    public static function provideIsValidClassNameCases(): iterable
    {
        yield from [
            'empty string' => [false, ''],
            'just a space' => [false, ' '],
            'class name with dot' => [false, 'ClassName.WithDot'],
            'too many backslashes' => [false, '\\\\TooManyBackSlashes'],
            'global class name' => [true, 'Foo'],
            'namespaced class name' => [true, Bar::class],
        ];
    }

    /**
     * @throws Throwable
     */
    public function testBlockForwardingToPartialObject(): void
    {
        $m = mock(new MockeryTestBar1(), [
            'foo' => 1,
            Container::BLOCKS => ['method1'],
        ]);
        self::assertSame($m, $m->method1());
        self::assertSame(1, $m->foo());
    }

    /**
     * @throws Throwable
     */
    public function testCallingSelfOnContainerWithoutMocksThrowsException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('You have not declared any mocks yet');

        Mockery::getContainer()->self();
    }

    /**
     * @issue issue/35
     *
     * @throws Throwable
     */
    public function testCallingSelfOnlyReturnsLastMockCreatedOrCurrentMockBeingProgrammedSinceTheyAreOneAndTheSame(): void
    {
        $m = mock(MockeryTestFoo::class);
        self::assertNotInstanceOf(MockeryTestFoo2::class, Mockery::self());
        // $m = mock('\PHP73\MockeryTestFoo2');
        // self::assertInstanceOf(MockeryTestFoo2::class, self());
        // $m = mock('\PHP73\MockeryTestFoo');
        // self::assertNotInstanceOf(MockeryTestFoo2::class, Mockery::self());
        // self::assertInstanceOf(MockeryTestFoo::class, Mockery::self());
    }

    /**
     * @throws Throwable
     */
    public function testCanCreateNonOverridenInstanceOfPreviouslyOverridenInternalClasses(): void
    {
        if (PHP_MAJOR_VERSION > 7) {
            $this->expectException(LogicException::class);
        }

        Mockery::getConfiguration()->setInternalClassMethodParamMap(DateTime::class, 'modify', ['&$string']);
        // @ used to avoid E_STRICT for incompatible signature
        @$m = mock(DateTime::class);
        self::assertInstanceOf(MockInterface::class, $m, 'Mocking failed, remove @ error suppresion to debug');
        $rc = new ReflectionClass($m);
        $rm = $rc->getMethod('modify');
        $params = $rm->getParameters();
        self::assertTrue($params[0]->isPassedByReference());

        Mockery::getConfiguration()->resetInternalClassMethodParamMaps();

        $m = mock(DateTime::class);
        self::assertInstanceOf(MockInterface::class, $m, 'Mocking failed');
        $rc = new ReflectionClass($m);
        $rm = $rc->getMethod('modify');
        $params = $rm->getParameters();
        self::assertFalse($params[0]->isPassedByReference());

        Mockery::getConfiguration()->resetInternalClassMethodParamMaps();
    }

    /**
     * @throws Throwable
     */
    public function testCanMockAbstractClassWithAbstractPublicMethod(): void
    {
        $m = mock(MockeryTest_AbstractWithAbstractPublicMethod::class);
        self::assertInstanceOf(MockeryTest_AbstractWithAbstractPublicMethod::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockAbstractWithAbstractProtectedMethod(): void
    {
        $m = mock(MockeryTest_AbstractWithAbstractMethod::class);
        self::assertInstanceOf(MockeryTest_AbstractWithAbstractMethod::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockClassAndApplyMultipleInterfaces(): void
    {
        $m = mock('\PHP73\MockeryTestFoo, \PHP73\MockeryTest_Interface1, \PHP73\MockeryTest_Interface2');
        self::assertInstanceOf(MockeryTestFoo::class, $m);
        self::assertInstanceOf(MockeryTest_Interface1::class, $m);
        self::assertInstanceOf(MockeryTest_Interface2::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockClassContainingAPublicWakeupMethod(): void
    {
        $m = mock(MockeryTest_Wakeup1::class);
        self::assertInstanceOf(MockeryTest_Wakeup1::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockClassContainingMagicCallMethod(): void
    {
        $m = mock(MockeryTest_Call1::class);
        self::assertInstanceOf(MockeryTest_Call1::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockClassContainingMagicCallMethodWithoutTypeHinting(): void
    {
        $m = mock(MockeryTest_Call2::class);
        self::assertInstanceOf(MockeryTest_Call2::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockClassUsingMagicCallMethodsInPlaceOfNormalMethods(): void
    {
        $m = Mockery::mock(Gateway::class);
        $m->shouldReceive('iDoSomethingReallyCoolHere')
            ->once();
        $m->iDoSomethingReallyCoolHere();
    }

    /**
     * @throws Throwable
     */
    public function testCanMockClassWhereMethodHasReferencedParameter(): void
    {
        self::assertInstanceOf(MockInterface::class, Mockery::mock(new MockeryTest_MethodParamRef()));
    }

    /**
     * @throws Throwable
     */
    public function testCanMockClassWithConstructor(): void
    {
        $m = mock(MockeryTest_ClassConstructor::class);
        self::assertInstanceOf(MockeryTest_ClassConstructor::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockClassWithConstructorNeedingClassArgs(): void
    {
        $m = mock(MockeryTest_ClassConstructor2::class);
        self::assertInstanceOf(MockeryTest_ClassConstructor2::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockClassWithReservedWordMethod(): void
    {
        if (! extension_loaded('redis')) {
            self::markTestSkipped('phpredis not installed');
        }

        self::assertInstanceOf(Redis::class, mock(Redis::class));
    }

    /**
     * @throws Throwable
     */
    public function testCanMockClassesThatDescendFromInternalClasses(): void
    {
        $mock = mock(MockeryTest_ClassThatDescendsFromInternalClass::class);
        self::assertInstanceOf(DateTime::class, $mock);
    }

    /**
     * @requires PHP <8.1
     *
     * @throws Throwable
     */
    #[RequiresPhp('<8.1.0')]
    public function testCanMockClassesThatImplementSerializable(): void
    {
        $mock = mock(MockeryTest_ClassThatImplementsSerializable::class);
        self::assertInstanceOf(Serializable::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockInterface(): void
    {
        $m = mock(MockeryTest_Interface::class);
        self::assertInstanceOf(MockeryTest_Interface::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockInterfaceWithAbstractMethod(): void
    {
        $m = mock(MockeryTest_InterfaceWithAbstractMethod::class);
        self::assertInstanceOf(MockeryTest_InterfaceWithAbstractMethod::class, $m);
        $m->shouldReceive('foo')
            ->andReturn(1);
        self::assertSame(1, $m->foo());
    }

    /**
     * @throws Throwable
     */
    public function testCanMockInterfaceWithPublicStaticMethod(): void
    {
        $m = mock(MockeryTest_InterfaceWithPublicStaticMethod::class);
        self::assertInstanceOf(MockeryTest_InterfaceWithPublicStaticMethod::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockInterfacesAlongsideTraversable(): void
    {
        $mock = mock('\stdClass, \ArrayAccess, \Countable, \Traversable');
        self::assertInstanceOf(stdClass::class, $mock);
        self::assertInstanceOf(ArrayAccess::class, $mock);
        self::assertInstanceOf(Countable::class, $mock);
        self::assertInstanceOf(Traversable::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockInterfacesExtendingTraversable(): void
    {
        $mock = mock(MockeryTest_InterfaceWithTraversable::class);
        self::assertInstanceOf(MockeryTest_InterfaceWithTraversable::class, $mock);
        self::assertInstanceOf(ArrayAccess::class, $mock);
        self::assertInstanceOf(Countable::class, $mock);
        self::assertInstanceOf(Traversable::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockInternalClassesThatImplementSerializable(): void
    {
        $mock = mock(ArrayObject::class);
        self::assertInstanceOf(Serializable::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockMethodsWithRequiredParamsThatHaveDefaultValues(): void
    {
        $mock = mock(MockeryTest_MethodWithRequiredParamWithDefaultValue::class);
        $mock->shouldIgnoreMissing();
        self::assertNull($mock->foo(123, null));
    }

    /**
     * @throws Throwable
     */
    public function testCanMockMultipleInterfaces(): void
    {
        $m = mock('\PHP73\MockeryTest_Interface1, \PHP73\MockeryTest_Interface2');
        self::assertInstanceOf(MockeryTest_Interface1::class, $m);
        self::assertInstanceOf(MockeryTest_Interface2::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockMultipleInterfacesThatMayNotExist(): void
    {
        $m = mock(
            'NonExistingClass, \PHP73\MockeryTest_Interface1, \PHP73\MockeryTest_Interface2, \Some\Thing\That\Doesnt\Exist',
        );
        self::assertInstanceOf(MockeryTest_Interface1::class, $m);
        self::assertInstanceOf(MockeryTest_Interface2::class, $m);
        self::assertInstanceOf(Exist::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockSpl(): void
    {
        $m = mock(SplFixedArray::class);
        self::assertInstanceOf(SplFixedArray::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockStaticMethods(): void
    {
        $m = mock('alias:MyNamespace\MyClass2');
        $m->shouldReceive('staticFoo')
            ->andReturn('bar');
        self::assertSame('bar', MyClass2::staticFoo());
    }

    /**
     * Real world version of
     * testCanOverrideExpectedParametersOfInternalPHPClassesToPreserveRefs
     *
     * @throws Throwable
     */
    public function testCanOverrideExpectedParametersOfExtensionPHPClassesToPreserveRefs(): void
    {
        if (! class_exists(MongoCollection::class, false)) {
            self::markTestSkipped('ext/mongo not installed');
        }
        if (PHP_MAJOR_VERSION > 7) {
            $this->expectException(LogicException::class);
        }
        Mockery::getConfiguration()->setInternalClassMethodParamMap(
            MongoCollection::class,
            'insert',
            ['&$data', '$options']
        );
        // @ used to avoid E_STRICT for incompatible signature
        @$m = mock(MongoCollection::class);
        self::assertInstanceOf(MockInterface::class, $m, 'Mocking failed, remove @ error suppresion to debug');
        $m->shouldReceive('insert')
            ->with(Mockery::on(static function (&$data) {
                $data['_id'] = 123;

                return true;
            }), Mockery::type('array'));
        $data = [
            'a' => 1,
            'b' => 2,
        ];
        $m->insert($data, []);
        self::assertArrayHasKey('_id', $data);
        self::assertSame(123, $data['_id']);
        Mockery::getConfiguration()->resetInternalClassMethodParamMaps();
    }

    /**
     * Meant to test the same logic as
     * testCanOverrideExpectedParametersOfExtensionPHPClassesToPreserveRefs,
     * but:
     * - doesn't require an extension
     * - isn't actually known to be used
     *
     * @throws Throwable
     */
    public function testCanOverrideExpectedParametersOfInternalPHPClassesToPreserveRefs(): void
    {
        if (PHP_MAJOR_VERSION > 7) {
            $this->expectException(LogicException::class);
        }

        Mockery::getConfiguration()->setInternalClassMethodParamMap(DateTime::class, 'modify', ['&$string']);
        // @ used to avoid E_STRICT for incompatible signature
        @$m = mock(DateTime::class);
        self::assertInstanceOf(MockInterface::class, $m, 'Mocking failed, remove @ error suppresion to debug');
        $m->shouldReceive('modify')
            ->with(Mockery::on(static function (&$string) {
                $string = 'foo';

                return true;
            }));
        $data = 'bar';
        $m->modify($data);
        self::assertSame('foo', $data);
        Mockery::getConfiguration()->resetInternalClassMethodParamMaps();
    }

    /**
     * @throws Throwable
     */
    public function testCanPartialMockObjectUsingMagicCallMethodsInPlaceOfNormalMethods(): void
    {
        $m = Mockery::mock(new Gateway());
        $m->shouldReceive('iDoSomethingReallyCoolHere')
            ->once();
        $m->iDoSomethingReallyCoolHere();
    }

    /**
     * @throws Throwable
     */
    public function testCanPartiallyMockANormalClass(): void
    {
        $m = mock(MockeryTest_PartialNormalClass::class . '[foo]');
        self::assertInstanceOf(MockeryTest_PartialNormalClass::class, $m);
        $m->shouldReceive('foo')
            ->andReturn('cba');
        self::assertSame('abc', $m->bar());
        self::assertSame('cba', $m->foo());
    }

    /**
     * @throws Throwable
     */
    public function testCanPartiallyMockANormalClassWith2Methods(): void
    {
        $m = mock(MockeryTest_PartialNormalClass2::class . '[foo, baz]');
        self::assertInstanceOf(MockeryTest_PartialNormalClass2::class, $m);
        $m->shouldReceive('foo')
            ->andReturn('cba');
        $m->shouldReceive('baz')
            ->andReturn('cba');
        self::assertSame('abc', $m->bar());
        self::assertSame('cba', $m->foo());
        self::assertSame('cba', $m->baz());
    }

    /**
     * @throws Throwable
     */
    public function testCanPartiallyMockAnAbstractClass(): void
    {
        $m = mock(MockeryTest_PartialAbstractClass::class . '[foo]');
        self::assertInstanceOf(MockeryTest_PartialAbstractClass::class, $m);
        $m->shouldReceive('foo')
            ->andReturn('cba');
        self::assertSame('abc', $m->bar());
        self::assertSame('cba', $m->foo());
    }

    /**
     * @throws Throwable
     */
    public function testCanPartiallyMockAnAbstractClassWith2Methods(): void
    {
        $m = mock(MockeryTest_PartialAbstractClass2::class . '[foo,baz]');
        self::assertInstanceOf(MockeryTest_PartialAbstractClass2::class, $m);
        $m->shouldReceive('foo')
            ->andReturn('cba');
        $m->shouldReceive('baz')
            ->andReturn('cba');
        self::assertSame('abc', $m->bar());
        self::assertSame('cba', $m->foo());
        self::assertSame('cba', $m->baz());
    }

    /**
     * @throws Throwable
     */
    public function testCanPartiallyMockObjectWhereMethodHasReferencedParameter(): void
    {
        self::assertInstanceOf(MockInterface::class, Mockery::mock(new MockeryTest_MethodParamRef2()));
    }

    /**
     * @throws Throwable
     */
    public function testCanUseBlacklistAndExpectionOnNonBlacklistedMethod(): void
    {
        $m = mock(MockeryTest_PartialNormalClass2::class . '[!foo]');
        $m->shouldReceive('bar')
            ->andReturn('test')
            ->once();
        self::assertSame('test', $m->bar());
    }

    /**
     * @throws Throwable
     */
    public function testCanUseEmptyMethodlist(): void
    {
        $m = mock(MockeryTest_PartialNormalClass2::class . '[]');
        self::assertInstanceOf(MockeryTest_PartialNormalClass2::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCanUseExclamationToBlacklistMethod(): void
    {
        $m = mock(MockeryTest_PartialNormalClass2::class . '[!foo]');
        self::assertSame('abc', $m->foo());
    }

    /**
     * @throws Throwable
     */
    public function testCantCallMethodWhenUsingBlacklistAndNoExpectation(): void
    {
        $m = mock(MockeryTest_PartialNormalClass2::class . '[!foo]');

        try {
            $m->bar();
        } catch (BadMethodCallException $e) {
            self::assertStringContainsString(
                'Received ' . get_class($m)
                . '::bar(), but no expectations were specified',
                $e->getMessage()
            );
            $e->dismiss();
        }
    }

    /**
     * @issue issue/21
     *
     * @throws Throwable
     */
    public function testClassDeclaringIssetDoesNotThrowException(): void
    {
        self::assertInstanceOf(MockInterface::class, mock(MockeryTest_IssetMethod::class));
    }

    /**
     * @issue issue/21
     *
     * @throws Throwable
     */
    public function testClassDeclaringUnsetDoesNotThrowException(): void
    {
        self::assertInstanceOf(MockInterface::class, mock(MockeryTest_UnsetMethod::class));
    }

    /**
     * @throws Throwable
     */
    public function testClassesWithFinalMethodsCanBeProperPartialMocks(): void
    {
        $m = mock(MockeryFoo4::class . '[bar]');
        $m->shouldReceive('bar')
            ->andReturn('baz');
        self::assertSame('baz', $m->foo());
        self::assertSame('baz', $m->bar());
        self::assertInstanceOf(MockeryFoo4::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testClassesWithFinalMethodsCanBeProperPartialMocksButFinalMethodsNotPartialed(): void
    {
        $m = mock(MockeryFoo4::class . '[foo]');
        $m->shouldReceive('foo')
            ->andReturn('foo');
        self::assertSame('baz', $m->foo()); // partial expectation ignored - will fail callcount assertion
        self::assertInstanceOf(MockeryFoo4::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testClassesWithFinalMethodsCanBeProxyPartialMocks(): void
    {
        $m = mock(new MockeryFoo4());
        $m->shouldReceive('foo')
            ->andReturn('baz');
        self::assertSame('baz', $m->foo());
        self::assertSame('bar', $m->bar());
        self::assertInstanceOf(MockeryFoo4::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCreatingAPartialAllowsDynamicExpectationsAndPassesThroughUnexpectedMethods(): void
    {
        $m = mock(new MockeryTestFoo());
        $m->shouldReceive('bar')
            ->andReturn('bar');
        self::assertSame('bar', $m->bar());
        self::assertSame('foo', $m->foo());
        self::assertInstanceOf(MockeryTestFoo::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCreatingAPartialAllowsExpectationsToInterceptCallsToImplementedMethods(): void
    {
        $m = mock(new MockeryTestFoo2());
        $m->shouldReceive('bar')
            ->andReturn('baz');
        self::assertSame('baz', $m->bar());
        self::assertSame('foo', $m->foo());
        self::assertInstanceOf(MockeryTestFoo2::class, $m);
    }

    /**
     * @issue issue/89
     *
     * @throws Throwable
     */
    public function testCreatingMockOfClassWithExistingToStringMethodDoesntCreateClassWithTwoToStringMethods(): void
    {
        $m = mock(MockeryTest_WithToString::class); // this would fatal
        $m->shouldReceive('__toString')
            ->andReturn('dave');
        self::assertSame('dave', "{$m}");
    }

    /**
     * @throws Throwable
     */
    public function testCreationOfInstanceMock(): void
    {
        $m = mock('overload:MyNamespace\MyClass4');
        self::assertInstanceOf(MyClass4::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testCreationOfInstanceMockWithFullyQualifiedName(): void
    {
        $m = mock('overload:\MyNamespace\MyClass11');
        self::assertInstanceOf(MyClass11::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testExceptionOutputMakesBooleansLookLikeBooleans(): void
    {
        $mock = mock('MyTestClass');
        $mock->shouldReceive('foo')
            ->with(123);

        $this->expectException(NoMatchingExpectationException::class);
        $this->expectExceptionMessage('MyTestClass::foo(true, false, [0 => true, 1 => false])');

        $mock->foo(true, false, [true, false]);
    }

    /**
     * @throws Throwable
     */
    public function testExistingStaticMethodMocking(): void
    {
        $mock = mock('\PHP73\MockeryTest_PartialStatic[mockMe]');

        $mock->shouldReceive('mockMe')
            ->with(5)
            ->andReturn(10);

        self::assertSame(10, $mock::mockMe(5));
        self::assertSame(3, $mock::keepMe(3));
    }

    /**
     * @throws Throwable
     */
    public function testFinalClassesCanBePartialMocks(): void
    {
        $m = mock(new MockeryFoo3());
        $m->shouldReceive('foo')
            ->andReturn('baz');
        self::assertSame('baz', $m->foo());
        self::assertNotInstanceOf(MockeryFoo3::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testGetExpectationCountFreshContainer(): void
    {
        self::assertSame(0, Mockery::getContainer()->mockery_getExpectationCount());
    }

    /**
     * @throws Throwable
     */
    public function testGetExpectationCountMockWithAtLeast(): void
    {
        $m = mock();
        $m->shouldReceive('foo')
            ->atLeast()
            ->once();
        self::assertSame(1, Mockery::getContainer()->mockery_getExpectationCount());
        $m->foo();
        $m->foo();
    }

    /**
     * @throws Throwable
     */
    public function testGetExpectationCountMockWithNever(): void
    {
        $m = mock();
        $m->shouldReceive('foo')
            ->never();
        self::assertSame(1, Mockery::getContainer()->mockery_getExpectationCount());
    }

    /**
     * @throws Throwable
     */
    public function testGetExpectationCountMockWithOnce(): void
    {
        $m = mock();
        $m->shouldReceive('foo')
            ->once();
        self::assertSame(1, Mockery::getContainer()->mockery_getExpectationCount());
        $m->foo();
    }

    /**
     * @throws Throwable
     */
    public function testGetExpectationCountStub(): void
    {
        $m = mock();
        $m->shouldReceive('foo');
        self::assertSame(0, Mockery::getContainer()->mockery_getExpectationCount());
    }

    /**
     * @throws Throwable
     */
    public function testGetKeyOfDemeterMockShouldReturnKeyWhenMatchingMock(): void
    {
        $m = mock();
        $m->shouldReceive('foo->bar');
        self::assertMatchesRegularExpression(
            '/Mockery_(\d+)__demeter_([0-9a-f]+)_foo/',
            Mockery::getContainer()->getKeyOfDemeterMockFor('foo', get_class($m))
        );
    }

    /**
     * @throws Throwable
     */
    public function testGetKeyOfDemeterMockShouldReturnNullWhenNoMatchingMock(): void
    {
        $method = 'unknownMethod';
        self::assertNull(Mockery::getContainer()->getKeyOfDemeterMockFor($method, 'any'));

        $m = mock();
        $m->shouldReceive('method');
        self::assertNull(Mockery::getContainer()->getKeyOfDemeterMockFor($method, get_class($m)));

        $m->shouldReceive('foo->bar');
        self::assertNull(Mockery::getContainer()->getKeyOfDemeterMockFor($method, get_class($m)));
    }

    /**
     * @throws Throwable
     */
    public function testHandlesMethodWithArgumentExpectationWhenCalledWithCircularArray(): void
    {
        $testArray = [];
        $testArray['myself'] = &$testArray;

        $mock = mock('MyTestClass');
        $mock->shouldReceive('foo')
            ->with([
                'yourself' => 21,
            ]);

        $this->expectException(NoMatchingExpectationException::class);
        $this->expectExceptionMessage("MyTestClass::foo(['myself' => [...]])");
        $mock->foo($testArray);
    }

    /**
     * @throws Throwable
     */
    public function testHandlesMethodWithArgumentExpectationWhenCalledWithNestedArray(): void
    {
        $testArray = [];
        $testArray['a_scalar'] = 2;
        $testArray['an_array'] = [1, 2, 3];

        $mock = mock('MyTestClass');
        $mock->shouldReceive('foo')
            ->with([
                'yourself' => 21,
            ]);

        $this->expectException(NoMatchingExpectationException::class);
        $this->expectExceptionMessage("MyTestClass::foo(['a_scalar' => 2, 'an_array' => [...]])");
        $mock->foo($testArray);
    }

    /**
     * @throws Throwable
     */
    public function testHandlesMethodWithArgumentExpectationWhenCalledWithNestedClosure(): void
    {
        $testArray = [];
        $testArray['a_scalar'] = 2;
        $testArray['a_closure'] = static function (): void {};

        $mock = mock('MyTestClass');
        $mock->shouldReceive('foo')
            ->with([
                'yourself' => 21,
            ]);

        $this->expectException(NoMatchingExpectationException::class);
        $this->expectExceptionMessage("MyTestClass::foo(['a_scalar' => 2, 'a_closure' => object(Closure");
        $mock->foo($testArray);
    }

    /**
     * @throws Throwable
     */
    public function testHandlesMethodWithArgumentExpectationWhenCalledWithNestedObject(): void
    {
        $testArray = [];
        $testArray['a_scalar'] = 2;
        $testArray['an_object'] = new stdClass();

        $mock = mock('MyTestClass');
        $mock->shouldReceive('foo')
            ->with([
                'yourself' => 21,
            ]);

        $this->expectException(NoMatchingExpectationException::class);
        $this->expectExceptionMessage("MyTestClass::foo(['a_scalar' => 2, 'an_object' => object(stdClass)])");
        $mock->foo($testArray);
    }

    /**
     * @throws Throwable
     */
    public function testHandlesMethodWithArgumentExpectationWhenCalledWithNestedResource(): void
    {
        $testArray = [];
        $testArray['a_scalar'] = 2;
        $testArray['a_resource'] = fopen('php://memory', 'r');

        $mock = mock('MyTestClass');
        $mock->shouldReceive('foo')
            ->with([
                'yourself' => 21,
            ]);

        $this->expectException(NoMatchingExpectationException::class);
        $this->expectExceptionMessage("MyTestClass::foo(['a_scalar' => 2, 'a_resource' => resource(...)])");
        $mock->foo($testArray);
    }

    /**
     * @throws Throwable
     */
    public function testHandlesMethodWithArgumentExpectationWhenCalledWithResource(): void
    {
        $mock = mock('MyTestClass');
        $mock->shouldReceive('foo')
            ->with([
                'yourself' => 21,
            ]);

        $this->expectException(NoMatchingExpectationException::class);
        $this->expectExceptionMessage('MyTestClass::foo(resource(...))');
        $mock->foo(fopen('php://memory', 'r'));
    }

    /**
     * @throws Throwable
     */
    public function testInstanceMocksShouldIgnoreMissing(): void
    {
        $m = mock('overload:MyNamespace\MyClass12');
        $m->shouldIgnoreMissing();

        $instance = new MyClass12();
        self::assertNull($instance->foo());
    }

    /**
     * @throws Throwable
     */
    public function testInstantiationOfInstanceMock(): void
    {
        $m = mock('overload:MyNamespace\MyClass5');
        $instance = new MyClass5();
        self::assertInstanceOf(MyClass5::class, $instance);
    }

    /**
     * @throws Throwable
     */
    public function testInstantiationOfInstanceMockImportsDefaultExpectations(): void
    {
        $m = mock('overload:MyNamespace\MyClass6');
        $m->shouldReceive('foo')
            ->andReturn('bar')
            ->byDefault();
        $instance = new MyClass6();

        self::assertSame('bar', $instance->foo());
    }

    /**
     * @throws Throwable
     */
    public function testInstantiationOfInstanceMockImportsDefaultExpectationsInTheCorrectOrder(): void
    {
        $m = mock('overload:MyNamespace\MyClass6');
        $m->shouldReceive('foo')
            ->andReturn(1)
            ->byDefault();
        $m->shouldReceive('foo')
            ->andReturn(2)
            ->byDefault();
        $m->shouldReceive('foo')
            ->andReturn(3)
            ->byDefault();
        $instance = new MyClass6();

        self::assertSame(3, $instance->foo());
    }

    /**
     * @throws Throwable
     */
    public function testInstantiationOfInstanceMockImportsExpectations(): void
    {
        $m = mock('overload:MyNamespace\MyClass6');
        $m->shouldReceive('foo')
            ->andReturn('bar');
        $instance = new MyClass6();
        self::assertSame('bar', $instance->foo());
    }

    /**
     * @throws Throwable
     */
    public function testInstantiationOfInstanceMockWithConstructorParameterValidation(): void
    {
        $m = mock('overload:MyNamespace\MyClass14');
        $params = [
            'value1' => uniqid('test_', false),
        ];
        $m->shouldReceive('__construct')
            ->with($params)
            ->once();

        new MyClass14($params);
    }

    /**
     * @throws Throwable
     */
    public function testInstantiationOfInstanceMockWithConstructorParameterValidationException(): void
    {
        $m = mock('overload:MyNamespace\MyClass16');
        $m->shouldReceive('__construct')
            ->andThrow(new Exception('instanceMock ' . random_int(100, 999)));

        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/^instanceMock \d{3}$/');
        new MyClass16();
    }

    /**
     * @throws Throwable
     */
    public function testInstantiationOfInstanceMockWithConstructorParameterValidationNegative(): void
    {
        $m = mock('overload:MyNamespace\MyClass15');
        $params = [
            'value1' => uniqid('test_', false),
        ];
        $m->shouldReceive('__construct')
            ->with($params);

        $this->expectException(NoMatchingExpectationException::class);
        new MyClass15([]);
    }

    /**
     * @throws Throwable
     */
    public function testInstantiationOfInstanceMocksAddsThemToContainerForVerification(): void
    {
        $m = mock('overload:MyNamespace\MyClass8');
        $m->shouldReceive('foo')
            ->once();
        $instance = new MyClass8();
        $this->expectException(Mockery\CountValidator\Exception::class);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testInstantiationOfInstanceMocksDoesNotHaveCountValidatorCrossover(): void
    {
        $m = mock('overload:MyNamespace\MyClass9');
        $m->shouldReceive('foo')
            ->once();
        $instance1 = new MyClass9();
        $instance2 = new MyClass9();
        $instance1->foo();
        $instance2->foo();
    }

    /**
     * @throws Throwable
     */
    public function testInstantiationOfInstanceMocksDoesNotHaveCountValidatorCrossover2(): void
    {
        $m = mock('overload:MyNamespace\MyClass10');
        $m->shouldReceive('foo')
            ->once();
        $instance1 = new MyClass10();
        $instance2 = new MyClass10();
        $instance1->foo();
        $this->expectException(Mockery\CountValidator\Exception::class);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testInstantiationOfInstanceMocksIgnoresVerificationOfOriginMock(): void
    {
        $m = mock('overload:MyNamespace\MyClass7');
        $m->shouldReceive('foo')
            ->once()
            ->andReturn('bar');
    }

    /**
     * @throws Throwable
     */
    public function testInterfacesCanHaveAssertions(): void
    {
        $m = mock('\stdClass, \ArrayAccess, \Countable, \Traversable');
        $m->shouldReceive('foo')
            ->once();
        $m->foo();
    }

    /**
     * @dataProvider provideIsValidClassNameCases
     *
     * @throws Throwable
     */
    #[DataProvider('provideIsValidClassNameCases')]
    public function testIsValidClassName($expected, $className): void
    {
        self::assertSame($expected, (new Container())->isValidClassName($className));
    }

    /**
     * @throws Throwable
     */
    public function testIssetMappingUsingProxiedPartialsCheckNoExceptionThrown(): void
    {
        $var = mock(new MockeryTestIsset_Bar());
        $mock = mock(new MockeryTestIsset_Foo($var));
        $mock->shouldReceive('bar')->once();
        $mock->bar();
    }

    /**
     * @throws Throwable
     */
    public function testMethodParamsPassedByReferenceHaveReferencePreserved(): void
    {
        $m = mock(MockeryTestRef1::class);
        $m->shouldReceive('foo')
            ->with(Mockery::on(static function (&$a) {
                $a++;

                return true;
            }), Mockery::any());
        $a = 1;
        $b = 1;
        $m->foo($a, $b);
        self::assertSame(2, $a);
        self::assertSame(1, $b);
    }

    /**
     * @throws Throwable
     */
    public function testMethodParamsPassedByReferenceThroughWithArgsHaveReferencePreserved(): void
    {
        $m = mock(MockeryTestRef1::class);
        $m->shouldReceive('foo')
            ->withArgs(static function (&$a, $b) {
                $a++;
                $b++;

                return true;
            });
        $a = 1;
        $b = 1;
        $m->foo($a, $b);
        self::assertSame(2, $a);
        self::assertSame(1, $b);
    }

    /**
     * @throws Throwable
     */
    public function testMethodsReturningParamsByReferenceDoesNotErrorOut(): void
    {
        mock(MockeryTest_ReturnByRef::class);
        $mock = mock(MockeryTest_ReturnByRef::class);
        $mock->shouldReceive('get')
            ->andReturn($var = 123);
        self::assertSame($var, $mock->get());
    }

    /**
     * @throws Throwable
     */
    public function testMockCallableTypeHint(): void
    {
        self::assertInstanceOf(MockInterface::class, mock(MockeryTest_MockCallableTypeHint::class));
    }

    /**
     * @throws Throwable
     */
    public function testMockedStaticMethodsObeyMethodCounting(): void
    {
        $m = mock('alias:MyNamespace\MyClass3');
        $m->shouldReceive('staticFoo')
            ->once()
            ->andReturn('bar');
        $this->expectException(Mockery\CountValidator\Exception::class);
        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testMockedStaticThrowsExceptionWhenMethodDoesNotExist(): void
    {
        $m = mock('alias:MyNamespace\StaticNoMethod');

        try {
            StaticNoMethod::staticFoo();
        } catch (BadMethodCallException $e) {
            self::assertStringContainsString(
                'StaticNoMethod::staticFoo() does not exist on this mock object',
                $e->getMessage()
            );
            // Mockery + PHPUnit has a fail safe for tests swallowing our
            // exceptions
            $e->dismiss();

            return;
        }

        self::fail('Exception was not thrown');
    }

    /**
     * @throws Throwable
     */
    public function testMockeryCloseForIllegalIssetFileInclude(): void
    {
        $m = Mockery::mock(stdClass::class)
            ->shouldReceive('get')
            ->andReturn(false)
            ->getMock();

        // no idea what this test does, adding this as an assertion...
        self::assertSame(false, $m->get());
    }

    /**
     * @throws Throwable
     */
    public function testMockeryDoesntTryAndMockLowercaseToString(): void
    {
        self::assertInstanceOf(MockInterface::class, mock(MockeryTest_Lowercase_ToString::class));
    }

    /**
     * @throws Throwable
     */
    public function testMockeryShouldCallConstructorByDefaultWhenRequestingPartials(): void
    {
        $mock = mock('\PHP73\EmptyConstructorTest[foo]');
        self::assertSame(0, $mock->numberOfConstructorArgs);
    }

    /**
     * @throws Throwable
     */
    public function testMockeryShouldDistinguishBetweenConstructorParamsAndClosures(): void
    {
        $obj = new MockeryTestFoo();
        self::assertInstanceOf(MockInterface::class, mock('\PHP73\MockeryTest_ClassMultipleConstructorParams[dave]', [
            &$obj, 'foo',
        ]));
    }

    /**
     * @throws Throwable
     */
    public function testMockeryShouldInterpretEmptyArrayAsConstructorArgs(): void
    {
        $mock = mock(EmptyConstructorTest::class, []);
        self::assertSame(0, $mock->numberOfConstructorArgs);
    }

    /**
     * @throws Throwable
     */
    public function testMockeryShouldNotMockCallstaticMagicMethod(): void
    {
        self::assertInstanceOf(MockInterface::class, mock(MockeryTest_CallStatic::class));
    }

    /**
     * @throws Throwable
     */
    public function testMockeryShouldRespectInterfaceWithMethodParamSelf(): void
    {
        self::assertInstanceOf(MockInterface::class, mock(MockeryTest_InterfaceWithMethodParamSelf::class));
    }

    /**
     * @throws Throwable
     */
    public function testMockingAConcreteObjectCreatesAPartialWithoutError(): void
    {
        $m = mock(new stdClass());
        $m->shouldReceive('foo')
            ->andReturn('bar');
        self::assertSame('bar', $m->foo());
        self::assertInstanceOf(stdClass::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testMockingAKnownConcreteClassCanBeGrantedAnArbitraryClassType(): void
    {
        $m = mock('alias:MyNamespace\MyClass');
        $m->shouldReceive('foo')
            ->andReturn('bar');

        self::assertSame('bar', $m->foo());
        self::assertInstanceOf(MyClass::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testMockingAKnownConcreteClassSoMockInheritsClassType(): void
    {
        $m = mock(stdClass::class);
        $m->shouldReceive('foo')
            ->andReturn('bar');
        self::assertSame('bar', $m->foo());
        self::assertInstanceOf(stdClass::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testMockingAKnownConcreteClassWithFinalMethodsThrowsNoException(): void
    {
        self::assertInstanceOf(MockInterface::class, mock(MockeryFoo4::class));
    }

    /**
     * @throws Throwable
     */
    public function testMockingAKnownConcreteFinalClassThrowsErrorsOnlyPartialMocksCanMockFinalElements(): void
    {
        $this->expectException(Mockery\Exception::class);
        $m = mock(MockeryFoo3::class);
    }

    /**
     * @throws Throwable
     */
    public function testMockingAKnownUserClassSoMockInheritsClassType(): void
    {
        $m = mock(MockeryTest_TestInheritedType::class);
        self::assertInstanceOf(MockeryTest_TestInheritedType::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testMockingAllowsPublicPropertyStubbingOnNamedMock(): void
    {
        $m = mock('Foo');
        $m->foo = 'bar';
        self::assertSame('bar', $m->foo);
        // self::assertArrayHasKey('foo', $m->mockery_getMockableProperties());
    }

    /**
     * @throws Throwable
     */
    public function testMockingAllowsPublicPropertyStubbingOnPartials(): void
    {
        $m = mock(new stdClass());
        $m->foo = 'bar';
        self::assertSame('bar', $m->foo);
        // self::assertArrayHasKey('foo', $m->mockery_getMockableProperties());
    }

    /**
     * @throws Throwable
     */
    public function testMockingAllowsPublicPropertyStubbingOnRealClass(): void
    {
        $m = mock(MockeryTestFoo::class);
        $m->foo = 'bar';
        self::assertSame('bar', $m->foo);
        // self::assertArrayHasKey('foo', $m->mockery_getMockableProperties());
    }

    /**
     * @throws Throwable
     */
    public function testMockingDoesNotStubNonStubbedPropertiesOnPartials(): void
    {
        $m = mock(new MockeryTest_ExistingProperty());
        self::assertSame('bar', $m->foo);
        self::assertArrayNotHasKey('foo', $m->mockery_getMockableProperties());
    }

    /**
     * @throws Throwable
     */
    public function testMockingInterfaceThatExtendsIteratorAggregateDoesNotImplementIterator(): void
    {
        $mock = mock(MockeryTest_InterfaceThatExtendsIteratorAggregate::class);
        self::assertInstanceOf(IteratorAggregate::class, $mock);
        self::assertInstanceOf(Traversable::class, $mock);
        self::assertNotInstanceOf(Iterator::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testMockingInterfaceThatExtendsIteratorDoesNotImplementIterator(): void
    {
        $mock = mock(MockeryTest_InterfaceThatExtendsIterator::class);
        self::assertInstanceOf(Iterator::class, $mock);
        self::assertInstanceOf(Traversable::class, $mock);
    }

    /**
     * @requires PHP < 8.0
     *
     * @throws Throwable
     */
    #[RequiresPhp('<8.0.0')]
    public function testMockingIteratorAggregateDoesNotImplementIterator(): void
    {
        $mock = mock(MockeryTest_ImplementsIteratorAggregate::class);
        self::assertInstanceOf(IteratorAggregate::class, $mock);
        self::assertInstanceOf(Traversable::class, $mock);
        self::assertNotInstanceOf(Iterator::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testMockingIteratorAggregateDoesNotImplementIteratorAlongside(): void
    {
        $mock = mock(IteratorAggregate::class);
        self::assertInstanceOf(IteratorAggregate::class, $mock);
        self::assertInstanceOf(Traversable::class, $mock);
        self::assertNotInstanceOf(Iterator::class, $mock);
    }

    /**
     * @requires PHP < 8.0
     *
     * @throws Throwable
     */
    #[RequiresPhp('<8.0.0')]
    public function testMockingIteratorDoesNotImplementIterator(): void
    {
        $mock = mock(MockeryTest_ImplementsIterator::class);
        self::assertInstanceOf(Iterator::class, $mock);
        self::assertInstanceOf(Traversable::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testMockingIteratorDoesNotImplementIteratorAlongside(): void
    {
        $mock = mock(Iterator::class);
        self::assertInstanceOf(Iterator::class, $mock);
        self::assertInstanceOf(Traversable::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testMockingPhpredisExtensionClassWorks(): void
    {
        if (! class_exists('Redis')) {
            self::markTestSkipped('PHPRedis extension required for this test');
        }
        self::assertInstanceOf(Redis::class, mock(Redis::class));
    }

    /**
     * @throws Throwable
     */
    public function testNamedMockMultipleInterfaces(): void
    {
        $m = mock('\stdClass, \ArrayAccess, \Countable', [
            'foo' => 1,
            'bar' => 2,
        ]);
        self::assertSame(1, $m->foo());
        self::assertSame(2, $m->bar());

        try {
            $m->f();
        } catch (BadMethodCallException $e) {
            self::assertTrue((bool) preg_match('/stdClass/', $e->getMessage()));
            self::assertTrue((bool) preg_match('/ArrayAccess/', $e->getMessage()));
            self::assertTrue((bool) preg_match('/Countable/', $e->getMessage()));
            $e->dismiss();
        }
    }

    /**
     * @throws Throwable
     */
    public function testNamedMockWithArrayDefs(): void
    {
        $m = mock(__FUNCTION__, [
            'foo' => 1,
            'bar' => 2,
        ]);
        self::assertSame(1, $m->foo());
        self::assertSame(2, $m->bar());

        try {
            $m->f();
        } catch (BadMethodCallException $e) {
            self::assertStringContainsString(__FUNCTION__, $e->getMessage());
            $e->dismiss();
        }
    }

    /**
     * @throws Throwable
     */
    public function testNamedMockWithArrayDefsCanBeOverridden(): void
    {
        // eg. In shared test setup
        $m = mock(__FUNCTION__, [
            'foo' => 1,
        ]);

        // and then overridden in one test
        $m->shouldReceive('foo')
            ->with('bar')
            ->once()
            ->andReturn(2);

        self::assertSame(2, $m->foo('bar'));

        try {
            $m->f();
        } catch (BadMethodCallException $e) {
            self::assertStringContainsString(__FUNCTION__, $e->getMessage());
            $e->dismiss();
        }
    }

    /**
     * @throws Throwable
     */
    public function testNamedMockWithConstructorArgs(): void
    {
        $m = mock(MockeryTest_ClassConstructor2::class . '[foo]', [$param1 = new stdClass()]);
        $m->shouldReceive('foo')
            ->andReturn(123);
        self::assertSame(123, $m->foo());
        self::assertSame($param1, $m->getParam1());
    }

    /**
     * @throws Throwable
     */
    public function testNamedMockWithConstructorArgsAndArrayDefs(): void
    {
        $m = mock(MockeryTest_ClassConstructor2::class . '[foo]', [$param1 = new stdClass()], [
            'foo' => 123,
        ]);
        self::assertSame(123, $m->foo());
        self::assertSame($param1, $m->getParam1());
    }

    /**
     * @throws Throwable
     */
    public function testNamedMockWithConstructorArgsButNoQuickDefsShouldLeaveConstructorIntact(): void
    {
        $m = mock(MockeryTest_ClassConstructor2::class, [$param1 = new stdClass()]);
        $m->makePartial();
        self::assertSame($param1, $m->getParam1());
    }

    /**
     * @throws Throwable
     */
    public function testNamedMockWithConstructorArgsWithInternalCallToMockedMethod(): void
    {
        $m = mock(MockeryTest_ClassConstructor2::class . '[foo]', [$param1 = new stdClass()]);
        $m->shouldReceive('foo')
            ->andReturn(123);
        self::assertSame(123, $m->bar());
    }

    /**
     * @throws Throwable
     */
    public function testNamedMockWithMakePartial(): void
    {
        $m = mock(MockeryTest_ClassConstructor2::class, [$param1 = new stdClass()]);
        $m->makePartial();
        self::assertSame('foo', $m->bar());
        $m->shouldReceive('bar')
            ->andReturn(123);
        self::assertSame(123, $m->bar());
    }

    /**
     * @throws Throwable
     */
    public function testNamedMockWithMakePartialThrowsIfNotAvailable(): void
    {
        $m = mock(MockeryTest_ClassConstructor2::class, [$param1 = new stdClass()]);
        $m->makePartial();

        try {
            $m->foorbar123();
        } catch (BadMethodCallException $e) {
            self::assertStringContainsString(
                'Method ' . get_class($m) . '::foorbar123() does not exist on this mock object',
                $e->getMessage()
            );
            $e->dismiss();
        }
        $m->mockery_verify();
    }

    /**
     * @throws Throwable
     */
    public function testNamedMocksAddNameToExceptions(): void
    {
        $m = mock(__FUNCTION__);
        $m->shouldReceive('foo')
            ->with(1)
            ->andReturn('bar');

        try {
            $m->foo();
        } catch (Mockery\Exception $e) {
            self::assertStringContainsString(__FUNCTION__, $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function testPartialWithArrayDefs(): void
    {
        $m = mock(new MockeryTestBar1(), [
            'foo' => 1,
            Container::BLOCKS => ['method1'],
        ]);
        self::assertSame(1, $m->foo());
    }

    /**
     * @throws Throwable
     */
    public function testPassingClosureAsFinalParameterUsedToDefineExpectations(): void
    {
        $m = mock('foo', static function ($m): void {
            $m->shouldReceive('foo')
                ->once()
                ->andReturn('bar');
        });
        self::assertSame('bar', $m->foo());
    }

    /**
     * @throws Throwable
     */
    public function testSettingPropertyOnInstanceMockWillSetItOnActualInstance(): void
    {
        $m = mock('overload:MyNamespace\MyClass13');
        $m->shouldReceive('foo')
            ->andSet('bar', 'baz');
        $instance = new MyClass13();
        $instance->foo();
        self::assertSame('baz', $m->bar);
        self::assertSame('baz', $instance->bar);
    }

    /**
     * @throws Throwable
     */
    public function testShouldThrowIfAttemptingToStubPrivateMethod(): void
    {
        $mock = mock(MockeryTest_WithProtectedAndPrivate::class);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('privateMethod() cannot be mocked as it is a private method');
        $mock->shouldReceive('privateMethod');
    }

    /**
     * @throws Throwable
     */
    public function testShouldThrowIfAttemptingToStubProtectedMethod(): void
    {
        $mock = mock(MockeryTest_WithProtectedAndPrivate::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'protectedMethod() cannot be mocked as it is a protected method and mocking protected methods is not enabled for the currently used mock object.',
        );
        $mock->shouldReceive('protectedMethod');
    }

    /**
     * @throws Throwable
     */
    public function testSimpleMockWithArrayDefs(): void
    {
        $m = mock([
            'foo' => 1,
            'bar' => 2,
        ]);
        self::assertSame(1, $m->foo());
        self::assertSame(2, $m->bar());
    }

    /**
     * @throws Throwable
     */
    public function testSimpleMockWithArrayDefsCanBeOverridden(): void
    {
        // eg. In shared test setup
        $m = mock([
            'foo' => 1,
            'bar' => 2,
        ]);

        // and then overridden in one test
        $m->shouldReceive('foo')
            ->with('baz')
            ->andReturn(2)
            ->once();

        $m->shouldReceive('bar')
            ->with('baz')
            ->andReturn(42)
            ->once();

        self::assertSame(2, $m->foo('baz'));
        self::assertSame(42, $m->bar('baz'));
    }

    /**
     * @throws Throwable
     */
    public function testSimplestMockCreation(): void
    {
        $m = mock();
        $m->shouldReceive('foo')->andReturn('bar');
        self::assertSame('bar', $m->foo());
    }

    /**
     * @throws Throwable
     */
    public function testSplClassWithFinalMethodsCanBeMocked(): void
    {
        $m = mock(SplFileInfo::class);
        $m->shouldReceive('foo')
            ->andReturn('baz');
        self::assertSame('baz', $m->foo());
        self::assertInstanceOf(SplFileInfo::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testSplClassWithFinalMethodsCanBeMockedMultipleTimes(): void
    {
        mock(SplFileInfo::class);
        $m = mock(SplFileInfo::class);
        $m->shouldReceive('foo')
            ->andReturn('baz');
        self::assertSame('baz', $m->foo());
        self::assertInstanceOf(SplFileInfo::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testSplfileinfoClassMockPassesUserExpectations(): void
    {
        $file = mock('\SplFileInfo[getFilename,getPathname,getExtension,getMTime]', [__FILE__]);
        $file->shouldReceive('getFilename')
            ->once()
            ->andReturn('foo');
        $file->shouldReceive('getPathname')
            ->once()
            ->andReturn('path/to/foo');
        $file->shouldReceive('getExtension')
            ->once()
            ->andReturn('css');
        $file->shouldReceive('getMTime')
            ->once()
            ->andReturn(time());

        // not sure what this test is for, maybe something special about
        // SplFileInfo
        self::assertSame('foo', $file->getFilename());
        self::assertSame('path/to/foo', $file->getPathname());
        self::assertSame('css', $file->getExtension());
        self::assertIsInt($file->getMTime());
    }

    /**
     * @throws Throwable
     */
    public function testThrowsExceptionIfClassNamesContainInvalidCharacters(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Class name contains invalid characters');

        $container = new Container();

        self::assertInstanceOf(MockInterface::class, $container->mock('ClassName.WithDot'));
    }

    /**
     * @throws Throwable
     */
    public function testThrowsExceptionIfClassOrInterfaceForPartialMockDoesNotExist(): void
    {
        $this->expectException(Mockery\Exception::class);

        mock('PartialNormalClassXYZ[foo]');
    }

    /**
     * @throws Throwable
     */
    public function testThrowsExceptionIfSettingExpectationForNonMockedMethodOfPartialMock(): void
    {
        self::markTestSkipped('For now...');
        $m = mock(MockeryTest_PartialNormalClass::class . '[foo]');
        self::assertInstanceOf(MockeryTest_PartialNormalClass::class, $m);
        $this->expectException(Mockery\Exception::class);
        $m->shouldReceive('bar')
            ->andReturn('cba');
    }

    /**
     * @throws Throwable
     */
    public function testThrowsWhenNamedMockClassExistsAndIsNotMockery(): void
    {
        $builder = new MockConfigurationBuilder();
        $builder->setName('DateTime');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not load mock DateTime, class already exists');
        $mock = mock($builder);
    }

    /**
     * @throws Throwable
     */
    public function testUndeclaredClassIsDeclared(): void
    {
        self::assertFalse(class_exists('BlahBlah'));
        $mock = mock('BlahBlah');
        self::assertInstanceOf('\BlahBlah', $mock);
    }

    /**
     * @throws Throwable
     */
    public function testUndeclaredClassWithNamespaceIncludingLeadingOperatorIsDeclared(): void
    {
        self::assertFalse(class_exists('\MyClasses\DaveBlah\BlahBlah'));
        $mock = mock('\MyClasses\DaveBlah\BlahBlah');
        self::assertInstanceOf('\MyClasses\DaveBlah\BlahBlah', $mock);
    }

    /**
     * @throws Throwable
     */
    public function testUndeclaredClassWithNamespaceIsDeclared(): void
    {
        self::assertFalse(class_exists('MyClasses\Blah\BlahBlah'));
        $mock = mock('MyClasses\Blah\BlahBlah');
        self::assertInstanceOf('\MyClasses\Blah\BlahBlah', $mock);
    }

    /**
     * @throws Throwable
     */
    public function testWakeupMagicIsNotMockedToAllowSerialisationInstanceHack(): void
    {
        self::assertInstanceOf(DateTime::class, mock(DateTime::class));
    }
}
