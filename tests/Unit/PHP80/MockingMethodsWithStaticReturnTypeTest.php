<?php

declare(strict_types=1);

namespace Tests\Unit\PHP80;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP80\MethodWithStaticReturnType;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingMethodsWithStaticReturnTypeTest extends MockeryTestCase
{
    public function testMockingStaticReturnType(): void
    {
        $mock = mock(MethodWithStaticReturnType::class);

        $mock->shouldReceive('returnType');

        self::assertSame($mock, $mock->returnType());
    }
}
