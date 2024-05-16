<?php

declare(strict_types=1);

namespace Tests\Unit\PHP80;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

/**
 * @coversDefaultClass \Mockery
 */
final class MockWithClosureAsLastArgumentTest extends MockeryTestCase
{
    public function testIfClosureIsPassedAsLastArgumentToMockItIsCalledWithMockObject(): void
    {
        $mock = Mockery::mock(
            TestInterface::class,
            static function (LegacyMockInterface|MockInterface $mock): void {
                $mock->expects('blm')->andReturn('#BlackLivesMatter');
            }
        );

        self::assertInstanceOf(TestInterface::class, $mock);

        self::assertSame('#BlackLivesMatter', $mock->blm());
    }
}

interface TestInterface
{
}
