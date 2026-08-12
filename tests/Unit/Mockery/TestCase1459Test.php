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

use Iterator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP74\RestrictReturnType;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery\Expectation
 *
 * @see https://github.com/mockery/mockery/issues/1459
 */
final class TestCase1459Test extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testCanMockAClassWithOverriddenReturnType(): void
    {
        $mock = mock(RestrictReturnType::class);

        self::assertInstanceOf(RestrictReturnType::class, $mock);
        self::assertInstanceOf(Iterator::class, $mock);
    }
}
