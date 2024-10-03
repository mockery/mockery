<?php

declare(strict_types=1);

namespace Tests\Unit\PHP73;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use PHP73\OldStyleConstructor;

/**
 * @coversDefaultClass \Mockery
 */
class MockingOldStyleConstructorTest extends MockeryTestCase
{
    public function testMockClassWithOldStyleConstructorAndArguments(): void
    {
        $double = \mock(OldStyleConstructor::class);

        self::assertInstanceOf(MockInterface::class, $double);
        self::assertInstanceOf(OldStyleConstructor::class, $double);
    }
}
