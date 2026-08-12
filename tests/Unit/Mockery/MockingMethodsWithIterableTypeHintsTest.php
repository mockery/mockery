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
use PHP73\MethodWithIterableTypeHints;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingMethodsWithIterableTypeHintsTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testItShouldSuccessfullyBuildTheMock(): void
    {
        $mock = mock(MethodWithIterableTypeHints::class);

        self::assertInstanceOf(MethodWithIterableTypeHints::class, $mock);
    }
}
