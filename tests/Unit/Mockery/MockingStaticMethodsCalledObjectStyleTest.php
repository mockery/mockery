<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\ClassWithStaticMethods;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingStaticMethodsCalledObjectStyleTest extends MockeryTestCase
{
    public function testProtectedStaticMethodCalledObjectStyleMockWithNotAllowingMockingOfNonExistentMethods(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = \mock(ClassWithStaticMethods::class);
        $mock->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('protectedBar')
            ->andReturn(true);
        self::assertTrue($mock->protectedBar());
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    public function testStaticMethodCalledObjectStyleMock(): void
    {
        $mock = \mock(ClassWithStaticMethods::class);
        $mock->shouldReceive('foo')
            ->andReturn(true);
        self::assertTrue($mock->foo());
    }

    public function testStaticMethodCalledObjectStyleMockWithNotAllowingMockingOfNonExistentMethods(): void
    {
        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $mock = \mock(ClassWithStaticMethods::class);
        $mock->shouldReceive('foo')
            ->andReturn(true);
        self::assertTrue($mock->foo());
        Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }
}
