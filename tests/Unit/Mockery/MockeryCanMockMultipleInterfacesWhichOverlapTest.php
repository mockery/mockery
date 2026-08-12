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
use Mockery\Container;
use PHP73\Chatroulette_ConnectionInterface;
use PHP73\Evenement_EventEmitter;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 */
final class MockeryCanMockMultipleInterfacesWhichOverlapTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testItSshouldNotDuplicateDoublyInheritedMethods(): void
    {
        $container = new Container();
        $mock = $container->mock(Evenement_EventEmitter::class, Chatroulette_ConnectionInterface::class);
        self::assertInstanceOf(Evenement_EventEmitter::class, $mock);
    }
}
