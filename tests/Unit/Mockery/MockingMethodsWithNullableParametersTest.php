<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\MethodWithNullableTypedParameter;
use PHP73\MethodWithParametersWithDefaultValues;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingMethodsWithNullableParametersTest extends MockeryTestCase
{
    public function testItCanHandleDefaultParameters(): void
    {
        $mock = \mock(MethodWithParametersWithDefaultValues::class);

        self::assertInstanceOf(MethodWithParametersWithDefaultValues::class, $mock);
    }

    public function testItCanHandleNullableTypedParameters(): void
    {
        $mock = \mock(MethodWithNullableTypedParameter::class);

        self::assertInstanceOf(MethodWithNullableTypedParameter::class, $mock);
    }
}
