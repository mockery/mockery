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
use PHP73\SemiReservedWordsAsMethods;
use Throwable;

use function method_exists;

/**
 * @coversDefaultClass \Mockery
 */
final class MockeryCanMockClassesWithSemiReservedWordsTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testMockSemiReservedWordsAsMethods(): void
    {
        $mock = Mockery::mock(SemiReservedWordsAsMethods::class);

        $mock->shouldReceive('include')
            ->andReturn('foo');

        self::assertTrue(method_exists($mock, 'include'));
        self::assertSame('foo', $mock->include());
    }
}
