<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP73;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use PHP73\OldStyleConstructor;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
class MockingOldStyleConstructorTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testMockClassWithOldStyleConstructorAndArguments(): void
    {
        $double = mock(OldStyleConstructor::class);

        self::assertInstanceOf(MockInterface::class, $double);
        self::assertInstanceOf(OldStyleConstructor::class, $double);
    }
}
