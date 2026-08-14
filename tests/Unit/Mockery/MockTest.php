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

use ErrorException;
use Exception;
use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception as MockeryException;
use Mockery\Exception\BadMethodCallException;
use Mockery\Mock;
use Mockery\MockInterface;
use Override;
use PHP73\ClassWithMethods;
use PHP73\ClassWithNoToString;
use PHP73\ClassWithProtectedMethod;
use PHP73\ClassWithToString;
use PHP73\ExampleClassForTestingNonExistentMethod;
use Throwable;

use function get_class;
use function method_exists;
use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockTest extends MockeryTestCase
{
    #[Override]
    public function mockeryTestTearDown(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    /**
     * @throws Throwable
     */
    public function testAnonymousMockWorksWithNotAllowingMockingOfNonExistentMethods(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);

        $m = mock();
        $m->shouldReceive('test123')
            ->andReturn(true);
        self::assertTrue($m->test123());
    }

    /**
     * @throws Throwable
     */
    public function testCallingShouldReceiveWithoutAValidMethodName(): void
    {
        $mock = Mockery::mock();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Received empty method name');

        $mock->shouldReceive('');
    }

    /**
     * @throws Throwable
     */
    public function testCanMockException(): void
    {
        $exception = Mockery::mock(Exception::class);
        self::assertInstanceOf(Exception::class, $exception);
    }

    /**
     * @throws Throwable
     */
    public function testCanMockSubclassOfException(): void
    {
        $errorException = Mockery::mock(ErrorException::class);
        self::assertInstanceOf(ErrorException::class, $errorException);
        self::assertInstanceOf(Exception::class, $errorException);
    }

    /**
     * @throws Throwable
     */
    public function testExpectationCountWillCountDefaultsIfNotOverriden(): void
    {
        $mock = new Mock();
        $mock->shouldReceive('doThis')
            ->once()
            ->byDefault();
        $mock->shouldReceive('doThat')
            ->once()
            ->byDefault();

        self::assertSame(2, $mock->mockery_getExpectationCount());
    }

    /**
     * @throws Throwable
     */
    public function testExpectationCountWillCountExpectations(): void
    {
        $mock = new Mock();
        $mock->shouldReceive('doThis')
            ->once();
        $mock->shouldReceive('doThat')
            ->once();

        self::assertSame(2, $mock->mockery_getExpectationCount());
    }

    /**
     * @throws Throwable
     */
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

        self::assertSame(2, $mock->mockery_getExpectationCount());
    }

    /**
     * @throws Throwable
     */
    public function testMockAddsToString(): void
    {
        $mock = mock(ClassWithNoToString::class);
        self::assertTrue(method_exists($mock, '__toString'));
    }

    /**
     * @throws Throwable
     */
    public function testMockToStringMayBeDeferred(): void
    {
        $mock = mock(ClassWithToString::class)->makePartial();
        self::assertSame('foo', (string) $mock);
    }

    /**
     * @throws Throwable
     */
    public function testMockToStringShouldIgnoreMissingAlwaysReturnsString(): void
    {
        $mock = mock(ClassWithNoToString::class)->shouldIgnoreMissing();
        self::assertNotSame('', (string) $mock);

        $mock->asUndefined();
        self::assertNotSame('', (string) $mock);
    }

    /**
     * @throws Throwable
     */
    public function testMockWithNotAllowingMockingOfNonExistentMethodsCanBeGivenAdditionalMethodsToMockEvenIfTheyDontExistOnClass(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $m = mock(ExampleClassForTestingNonExistentMethod::class);
        $m->shouldAllowMockingMethod('testSomeNonExistentMethod');
        $m->shouldReceive('testSomeNonExistentMethod')
            ->andReturn(true)
            ->once();
        self::assertTrue($m->testSomeNonExistentMethod());
    }

    /**
     * @throws Throwable
     */
    public function testProtectedMethodMockWithNotAllowingMockingOfNonExistentMethodsWhenShouldAllowMockingProtectedMethodsIsCalled(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $m = mock(ClassWithProtectedMethod::class);
        $m->shouldAllowMockingProtectedMethods();
        $m->shouldReceive('foo')
            ->andReturn(true);
        self::assertTrue($m->foo());
    }

    /**
     * @throws Throwable
     */
    public function testShouldAllowMockingMethodReturnsMockInstance(): void
    {
        $m = Mockery::mock('someClass');
        self::assertInstanceOf(MockInterface::class, $m->shouldAllowMockingMethod('testFunction'));
    }

    /**
     * @throws Throwable
     */
    public function testShouldAllowMockingProtectedMethodReturnsMockInstance(): void
    {
        $m = Mockery::mock('someClass');
        self::assertInstanceOf(MockInterface::class, $m->shouldAllowMockingProtectedMethods('testFunction'));
    }

    /**
     * @throws Throwable
     */
    public function testShouldIgnoreMissing(): void
    {
        $mock = mock(ClassWithNoToString::class)->shouldIgnoreMissing();
        self::assertNull($mock->nonExistingMethod());
    }

    /**
     * @throws Throwable
     */
    public function testShouldIgnoreMissingCallingExistentMethods(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = mock(ClassWithMethods::class)->shouldIgnoreMissing();

        self::assertNull($mock->foo());

        $mock->shouldReceive('bar')
            ->passthru();

        self::assertSame('bar', $mock->bar());
    }

    /**
     * @throws Throwable
     */
    public function testShouldIgnoreMissingCallingNonExistentMethods(): void
    {
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

    /**
     * @throws Throwable
     */
    public function testShouldIgnoreMissingCallingNonExistentMethodsUsingGlobalConfiguration(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);

        $mock = mock(ClassWithMethods::class)->shouldIgnoreMissing();

        try {
            $mock->nonExistentMethod();
        } catch (BadMethodCallException $e) {
            self::assertStringContainsString(
                'Method ' . get_class($mock) . '::nonExistentMethod() does not exist on this mock object',
                $e->getMessage()
            );

            $e->dismiss();
        }
    }

    /**
     * @throws Throwable
     */
    public function testShouldIgnoreMissingDisallowMockingNonExistentMethodsUsingGlobalConfiguration(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = mock(ClassWithMethods::class)->shouldIgnoreMissing();

        $this->expectException(MockeryException::class);
        $this->expectExceptionMessage(
            "Mockery's configuration currently forbids mocking the method nonExistentMethod as it does not exist on the class or object being mocked"
        );

        $mock->shouldReceive('nonExistentMethod');
    }

    /**
     * @throws Throwable
     */
    public function testShouldThrowExceptionWithInvalidClassName(): void
    {
        $this->expectException(MockeryException::class);
        $this->expectExceptionMessage('Class name contains invalid characters');

        mock('ClassName.CannotContainDot');
    }
}
