<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\ReturnTypeIterableTypeHint;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockClassWithIterableReturnTypeTest extends MockeryTestCase
{
    public function testMockingIterableReturnType(): void
    {
        $mock = mock(ReturnTypeIterableTypeHint::class);

        $mock->expects('returnIterable');

        self::assertSame([], $mock->returnIterable());
    }
}
