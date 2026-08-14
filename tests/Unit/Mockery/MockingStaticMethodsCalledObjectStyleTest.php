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
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Override;
use PHP73\ClassWithStaticMethods;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingStaticMethodsCalledObjectStyleTest extends MockeryTestCase
{
    #[Override]
    public function mockeryTestTearDown(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    /**
     * @throws Throwable
     */
    public function testProtectedStaticMethodCalledObjectStyleMockWithNotAllowingMockingOfNonExistentMethods(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = mock(ClassWithStaticMethods::class);
        $mock->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('protectedBar')->andReturn(true);
        self::assertTrue($mock->protectedBar());
    }

    /**
     * @throws Throwable
     */
    public function testStaticMethodCalledObjectStyleMock(): void
    {
        $mock = mock(ClassWithStaticMethods::class);
        $mock->shouldReceive('foo')
            ->andReturn(true);
        self::assertTrue($mock->foo());
    }

    /**
     * @throws Throwable
     */
    public function testStaticMethodCalledObjectStyleMockWithNotAllowingMockingOfNonExistentMethods(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = mock(ClassWithStaticMethods::class);
        $mock->shouldReceive('foo')->andReturn(true);
        self::assertTrue($mock->foo());
    }
}
