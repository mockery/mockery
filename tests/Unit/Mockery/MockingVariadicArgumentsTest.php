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
use PHP73\TestWithVariadicArguments;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingVariadicArgumentsTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testShouldAllowMockingVariadicArguments(): void
    {
        $mock = mock(TestWithVariadicArguments::class);

        $mock->shouldReceive('foo')
            ->andReturn('notbar');
        self::assertSame('notbar', $mock->foo());
    }
}
