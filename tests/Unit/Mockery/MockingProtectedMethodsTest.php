<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\TestIncreasedVisibilityChild;
use PHP73\TestWithProtectedMethods;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingProtectedMethodsTest extends MockeryTestCase
{
    public function testShouldAllowMockingAbstractProtectedMethods(): void
    {
        $mock = \mock(TestWithProtectedMethods::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive('abstractProtected')
            ->andReturn('abstractProtected');
        self::assertSame('abstractProtected', $mock->foo());
    }

    public function testShouldAllowMockingIncreasedVisabilityMethods(): void
    {
        $mock = \mock(TestIncreasedVisibilityChild::class);
        $mock->shouldReceive('foobar')
            ->andReturn('foobar');
        self::assertSame('foobar', $mock->foobar());
    }

    public function testShouldAllowMockingProtectedMethodOnDefinitionTimePartial(): void
    {
        $mock = \mock(TestWithProtectedMethods::class . '[protectedBar]')
            ->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive('protectedBar')
            ->andReturn('notbar');
        self::assertSame('notbar', $mock->bar());
    }

    public function testShouldAllowMockingProtectedMethods(): void
    {
        $mock = \mock(TestWithProtectedMethods::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive('protectedBar')
            ->andReturn('notbar');
        self::assertSame('notbar', $mock->bar());
    }

    /**
     * This is a regression test, basically we don't want the mock handling
     * interfering with calling protected methods partials
     */
    public function testShouldAutomaticallyDeferCallsToProtectedMethodsForPartials(): void
    {
        $mock = \mock(TestWithProtectedMethods::class . '[foo]');

        self::assertSame('bar', $mock->bar());
    }

    /**
     * This is a regression test, basically we don't want the mock handling
     * interfering with calling protected methods partials
     */
    public function testShouldAutomaticallyDeferCallsToProtectedMethodsForRuntimePartials(): void
    {
        $mock = \mock(TestWithProtectedMethods::class)->makePartial();
        self::assertSame('bar', $mock->bar());
    }

    public function testShouldAutomaticallyIgnoreAbstractProtectedMethods(): void
    {
        $mock = \mock(TestWithProtectedMethods::class)->makePartial();
        self::assertNull($mock->foo());
    }
}
