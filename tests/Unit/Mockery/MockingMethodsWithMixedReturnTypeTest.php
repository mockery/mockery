<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\MyInterface;

/**
 * @coversDefaultClass \Mockery
 */
class MockingMethodsWithMixedReturnTypeTest extends MockeryTestCase
{
    public function testMockingMixedReturnType(): void
    {
        $mock = Mockery::mock(MyInterface::class);

        $mock->shouldReceive('foo->bar')
            ->andReturn('bar');

        self::assertSame('bar', $mock->foo()->bar());
    }
}
