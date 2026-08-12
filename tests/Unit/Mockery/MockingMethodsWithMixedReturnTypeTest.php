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
use PHP73\MyInterface;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 */
class MockingMethodsWithMixedReturnTypeTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testMockingMixedReturnType(): void
    {
        $mock = Mockery::mock(MyInterface::class);

        $mock->shouldReceive('foo->bar')
            ->andReturn('bar');

        self::assertSame('bar', $mock->foo()->bar());
    }
}
