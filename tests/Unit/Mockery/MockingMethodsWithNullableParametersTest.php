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
use PHP73\MethodWithNullableTypedParameter;
use PHP73\MethodWithParametersWithDefaultValues;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingMethodsWithNullableParametersTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testItCanHandleDefaultParameters(): void
    {
        $mock = mock(MethodWithParametersWithDefaultValues::class);

        self::assertInstanceOf(MethodWithParametersWithDefaultValues::class, $mock);
    }

    /**
     * @throws Throwable
     */
    public function testItCanHandleNullableTypedParameters(): void
    {
        $mock = mock(MethodWithNullableTypedParameter::class);

        self::assertInstanceOf(MethodWithNullableTypedParameter::class, $mock);
    }
}
