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

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHP80\PHP80TestInterface;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 *
 * @requires PHP 8.0.0-dev
 */
final class MockWithClosureAsLastArgumentTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testIfClosureIsPassedAsLastArgumentToMockItIsCalledWithMockObject(): void
    {
        $mock = Mockery::mock(
            PHP80TestInterface::class,
            static function (LegacyMockInterface|MockInterface $mock): void {
                $mock->expects('blm')
                    ->andReturn('#BlackLivesMatter');
            }
        );

        self::assertInstanceOf(PHP80TestInterface::class, $mock);

        self::assertSame('#BlackLivesMatter', $mock->blm());
    }
}
