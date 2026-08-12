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
use Mockery\Exception;
use PHP73\UnmockableClass;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class ProxyMockingTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testFinalClassCannotBeMocked(): void
    {
        $this->expectException(Exception::class);

        mock(UnmockableClass::class);
    }

    /**
     * @throws Throwable
     */
    public function testPassesThruAnyMethod(): void
    {
        $mock = mock(new UnmockableClass());

        self::assertSame(1, $mock->anyMethod());
    }

    /**
     * @throws Throwable
     */
    public function testPassesThruVirtualMethods(): void
    {
        $mock = mock(new UnmockableClass());

        self::assertSame(42, $mock->theAnswer());
    }
}
