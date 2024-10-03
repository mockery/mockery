<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery;
use PHP73\SemiReservedWordsAsMethods;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Mockery
 */
final class MockeryCanMockClassesWithSemiReservedWordsTest extends TestCase
{
    public function testMockSemiReservedWordsAsMethods(): void
    {
        $mock = Mockery::mock(SemiReservedWordsAsMethods::class);

        $mock->shouldReceive('include')
            ->andReturn('foo');

        self::assertTrue(\method_exists($mock, 'include'));
        self::assertSame('foo', $mock->include());
    }
}
