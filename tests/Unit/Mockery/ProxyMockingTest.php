<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Exception;
use PHP73\UnmockableClass;

/**
 * @coversDefaultClass \Mockery
 */
final class ProxyMockingTest extends MockeryTestCase
{
    public function testFinalClassCannotBeMocked(): void
    {
        $this->expectException(Exception::class);

        \mock(UnmockableClass::class);
    }

    public function testPassesThruAnyMethod(): void
    {
        $mock = \mock(new UnmockableClass());

        self::assertSame(1, $mock->anyMethod());
    }

    public function testPassesThruVirtualMethods(): void
    {
        $mock = \mock(new UnmockableClass());

        self::assertSame(42, $mock->theAnswer());
    }
}
