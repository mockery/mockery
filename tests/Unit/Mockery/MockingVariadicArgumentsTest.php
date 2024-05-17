<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\TestWithVariadicArguments;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingVariadicArgumentsTest extends MockeryTestCase
{
    public function testShouldAllowMockingVariadicArguments(): void
    {
        $mock = mock(TestWithVariadicArguments::class);

        $mock->shouldReceive('foo')
            ->andReturn('notbar');
        self::assertEquals('notbar', $mock->foo());
    }
}
