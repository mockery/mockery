<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP80;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP80\MethodWithStaticReturnType;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 *
 * @requires PHP 8.0.0-dev
 */
final class MockingMethodsWithStaticReturnTypeTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testMockingStaticReturnType(): void
    {
        $mock = mock(MethodWithStaticReturnType::class);

        $mock->shouldReceive('returnType');

        self::assertSame($mock, $mock->returnType());
    }
}
