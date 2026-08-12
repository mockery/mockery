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
use PHP73\ReturnTypeIterableTypeHint;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockClassWithIterableReturnTypeTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testMockingIterableReturnType(): void
    {
        $mock = mock(ReturnTypeIterableTypeHint::class);

        $mock->expects('returnIterable');

        self::assertSame([], $mock->returnIterable());
    }
}
