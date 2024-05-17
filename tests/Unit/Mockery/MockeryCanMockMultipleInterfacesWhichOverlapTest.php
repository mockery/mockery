<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Container;
use PHP73\Chatroulette_ConnectionInterface;
use PHP73\Evenement_EventEmitter;

/**
 * @coversDefaultClass \Mockery
 */
final class MockeryCanMockMultipleInterfacesWhichOverlapTest extends MockeryTestCase
{
    public function testItSshouldNotDuplicateDoublyInheritedMethods(): void
    {
        $container = new Container();
        $mock = $container->mock(Evenement_EventEmitter::class, Chatroulette_ConnectionInterface::class);
        self::assertInstanceOf(Evenement_EventEmitter::class, $mock);
    }
}
