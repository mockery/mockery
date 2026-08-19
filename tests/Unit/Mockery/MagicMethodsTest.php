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

use Mockery;
use Mockery\Exception\InvalidCountException;
use PHP74\MagicMethod\ClassWithCloneMethod;
use PHP74\MagicMethod\ClassWithConstructorAndCloneMethod;
use PHP74\MagicMethod\InterfaceWithCloneMethod;
use Tests\Unit\AbstractTestCase;
use Throwable;

use function get_class;
use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MagicMethodsTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    public function testCloneMethodFailsWhenExpectationIsNotMet(): void
    {
        $mock = mock(ClassWithCloneMethod::class);
        $mock->expects('__clone')->twice();

        self::assertInstanceOf(InterfaceWithCloneMethod::class, clone $mock);

        $this->expectException(InvalidCountException::class);
        $this->assertInvalidCountExceptionMessage('__clone(<Any Arguments>)', get_class($mock), 2, 1);

        Mockery::close();
    }

    /**
     * @throws Throwable
     */
    public function testCloneMethodWorksWithExpectation(): void
    {
        $callCount = 0;
        $mock = mock(ClassWithCloneMethod::class);
        $mock->expects('__clone')
            ->twice()
            ->andReturnUsing(static function () use (&$callCount): void {
                ++$callCount;
            });

        self::assertInstanceOf(ClassWithCloneMethod::class, clone $mock);
        self::assertInstanceOf(InterfaceWithCloneMethod::class, clone $mock);
        self::assertSame(2, $callCount);
    }

    /**
     * Make sure existing projects that already call `clone` on a mock object without defining
     * a `__clone` expectation continue to work as expected.
     *
     * @throws Throwable
     */
    public function testCloneMethodWorksWithoutExpectationForBackwardsCompatibility(): void
    {
        $mock = mock(InterfaceWithCloneMethod::class);
        self::assertInstanceOf(InterfaceWithCloneMethod::class, clone $mock);
    }

    /**
     * @throws Throwable
     */
    public function testPartialMockCallsRealCloneMethodWithoutExpectation(): void
    {
        $mock = mock(ClassWithConstructorAndCloneMethod::class)->makePartial();
        $mock->__construct('value');

        self::assertSame('value cloned', (clone $mock)->value);
    }

    /**
     * @throws Throwable
     */
    public function testPartialMockCallsRealConstructorWithoutExpectation(): void
    {
        $mock = mock(ClassWithConstructorAndCloneMethod::class)->makePartial();
        $mock->__construct('value');

        self::assertSame('value', $mock->value);
    }
}
