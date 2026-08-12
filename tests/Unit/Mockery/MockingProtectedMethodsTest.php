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

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\TestIncreasedVisibilityChild;
use PHP73\TestWithProtectedMethods;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingProtectedMethodsTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testShouldAllowMockingAbstractProtectedMethods(): void
    {
        $mock = mock(TestWithProtectedMethods::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive('abstractProtected')
            ->andReturn('abstractProtected');
        self::assertSame('abstractProtected', $mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testShouldAllowMockingIncreasedVisabilityMethods(): void
    {
        $mock = mock(TestIncreasedVisibilityChild::class);
        $mock->shouldReceive('foobar')
            ->andReturn('foobar');
        self::assertSame('foobar', $mock->foobar());
    }

    /**
     * @throws Throwable
     */
    public function testShouldAllowMockingProtectedMethodOnDefinitionTimePartial(): void
    {
        $mock = mock(TestWithProtectedMethods::class . '[protectedBar]')
            ->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive('protectedBar')
            ->andReturn('notbar');
        self::assertSame('notbar', $mock->bar());
    }

    /**
     * @throws Throwable
     */
    public function testShouldAllowMockingProtectedMethods(): void
    {
        $mock = mock(TestWithProtectedMethods::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive('protectedBar')
            ->andReturn('notbar');
        self::assertSame('notbar', $mock->bar());
    }

    /**
     * This is a regression test, basically we don't want the mock handling
     * interfering with calling protected methods partials
     *
     * @throws Throwable
     */
    public function testShouldAutomaticallyDeferCallsToProtectedMethodsForPartials(): void
    {
        $mock = mock(TestWithProtectedMethods::class . '[foo]');

        self::assertSame('bar', $mock->bar());
    }

    /**
     * This is a regression test, basically we don't want the mock handling
     * interfering with calling protected methods partials
     *
     * @throws Throwable
     */
    public function testShouldAutomaticallyDeferCallsToProtectedMethodsForRuntimePartials(): void
    {
        $mock = mock(TestWithProtectedMethods::class)->makePartial();
        self::assertSame('bar', $mock->bar());
    }

    /**
     * @throws Throwable
     */
    public function testShouldAutomaticallyIgnoreAbstractProtectedMethods(): void
    {
        $mock = mock(TestWithProtectedMethods::class)->makePartial();
        self::assertNull($mock->foo());
    }
}
