<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use BadMethodCallException;
use ErrorException;
use Exception;
use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception as MockeryException;
use Mockery\Mock;
use Mockery\MockInterface;
use PHP73\ClassWithMethods;
use PHP73\ClassWithNoToString;
use PHP73\ClassWithProtectedMethod;
use PHP73\ClassWithToString;
use PHP73\ExampleClassForTestingNonExistentMethod;

use function method_exists;
use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockTest extends MockeryTestCase
{
    public function testAnonymousMockWorksWithNotAllowingMockingOfNonExistentMethods(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);

        $m = mock();
        $m->shouldReceive('test123')
            ->andReturn(true);
        self::assertTrue($m->test123());

        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    public function testCallingShouldReceiveWithoutAValidMethodName(): void
    {
        $mock = Mockery::mock();

        $this->expectException(InvalidArgumentException::class, 'Received empty method name');
        $mock->shouldReceive('');
    }

    public function testCanMockException(): void
    {
        $exception = Mockery::mock(Exception::class);
        self::assertInstanceOf(Exception::class, $exception);
    }

    public function testCanMockSubclassOfException(): void
    {
        $errorException = Mockery::mock(ErrorException::class);
        self::assertInstanceOf(ErrorException::class, $errorException);
        self::assertInstanceOf(Exception::class, $errorException);
    }

    public function testExpectationCountWillCountDefaultsIfNotOverriden(): void
    {
        $mock = new Mock();
        $mock->shouldReceive('doThis')
            ->once()
            ->byDefault();
        $mock->shouldReceive('doThat')
            ->once()
            ->byDefault();

        self::assertEquals(2, $mock->mockery_getExpectationCount());
    }

    public function testExpectationCountWillCountExpectations(): void
    {
        $mock = new Mock();
        $mock->shouldReceive('doThis')
            ->once();
        $mock->shouldReceive('doThat')
            ->once();

        self::assertEquals(2, $mock->mockery_getExpectationCount());
    }

    public function testExpectationCountWillIgnoreDefaultsIfOverriden(): void
    {
        $mock = new Mock();
        $mock->shouldReceive('doThis')
            ->once()
            ->byDefault();
        $mock->shouldReceive('doThis')
            ->twice();
        $mock->shouldReceive('andThis')
            ->twice();

        self::assertEquals(2, $mock->mockery_getExpectationCount());
    }

    public function testMockAddsToString(): void
    {
        $mock = mock(ClassWithNoToString::class);
        self::assertTrue(method_exists($mock, '__toString'));
    }

    public function testMockToStringMayBeDeferred(): void
    {
        $mock = mock(ClassWithToString::class)->makePartial();
        self::assertEquals('foo', (string) $mock);
    }

    public function testMockToStringShouldIgnoreMissingAlwaysReturnsString(): void
    {
        $mock = mock(ClassWithNoToString::class)->shouldIgnoreMissing();
        self::assertNotEquals('', (string) $mock);

        $mock->asUndefined();
        self::assertNotEquals('', (string) $mock);
    }

    public function testMockWithNotAllowingMockingOfNonExistentMethodsCanBeGivenAdditionalMethodsToMockEvenIfTheyDontExistOnClass(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $m = mock(ExampleClassForTestingNonExistentMethod::class);
        $m->shouldAllowMockingMethod('testSomeNonExistentMethod');
        $m->shouldReceive('testSomeNonExistentMethod')
            ->andReturn(true)
            ->once();
        self::assertTrue($m->testSomeNonExistentMethod());
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    public function testProtectedMethodMockWithNotAllowingMockingOfNonExistentMethodsWhenShouldAllowMockingProtectedMethodsIsCalled(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $m = mock(ClassWithProtectedMethod::class);
        $m->shouldAllowMockingProtectedMethods();
        $m->shouldReceive('foo')
            ->andReturn(true);
        self::assertTrue($m->foo());
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    public function testShouldAllowMockingMethodReturnsMockInstance(): void
    {
        $m = Mockery::mock('someClass');
        self::assertInstanceOf(MockInterface::class, $m->shouldAllowMockingMethod('testFunction'));
    }

    public function testShouldAllowMockingProtectedMethodReturnsMockInstance(): void
    {
        $m = Mockery::mock('someClass');
        self::assertInstanceOf(MockInterface::class, $m->shouldAllowMockingProtectedMethods('testFunction'));
    }

    public function testShouldIgnoreMissing(): void
    {
        $mock = mock(ClassWithNoToString::class)->shouldIgnoreMissing();
        self::assertNull($mock->nonExistingMethod());
    }

    public function testShouldIgnoreMissingCallingExistentMethods(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = mock(ClassWithMethods::class)->shouldIgnoreMissing();

        self::assertNull($mock->foo());

        $mock->shouldReceive('bar')
            ->passthru();

        self::assertSame('bar', $mock->bar());
    }

    public function testShouldIgnoreMissingCallingNonExistentMethods(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
        $mock = mock(ClassWithMethods::class)->shouldIgnoreMissing();

        self::assertNull($mock->foo());
        self::assertNull($mock->bar());
        self::assertNull($mock->nonExistentMethod());

        $mock->shouldReceive([
            'foo' => 'new_foo',
            'nonExistentMethod' => 'result',
        ]);
        $mock->shouldReceive('bar')
            ->passthru();

        self::assertSame('new_foo', $mock->foo());
        self::assertSame('bar', $mock->bar());
        self::assertSame('result', $mock->nonExistentMethod());
    }

    public function testShouldIgnoreMissingCallingNonExistentMethodsUsingGlobalConfiguration(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = mock(ClassWithMethods::class)->shouldIgnoreMissing();
        $this->expectException(BadMethodCallException::class);
        $mock->nonExistentMethod();
    }

    public function testShouldIgnoreMissingDisallowMockingNonExistentMethodsUsingGlobalConfiguration(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = mock(ClassWithMethods::class)->shouldIgnoreMissing();
        $this->expectException(MockeryException::class);
        $mock->shouldReceive('nonExistentMethod');
    }

    public function testShouldThrowExceptionWithInvalidClassName(): void
    {
        $this->expectException(MockeryException::class);
        mock('ClassName.CannotContainDot');
    }
}
