<?php

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */
namespace MockeryTest\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Container;
use MockeryTest\Fixture\Chatroulette_ConnectionInterface;
use MockeryTest\Fixture\Evenement_EventEmitter;

class GeneratorTest extends MockeryTestCase
{
    /** @test */
    public function shouldNotDuplicateDoublyInheritedMethods()
    {
        $container = new Container();
        $mock = $container->mock(Evenement_EventEmitter::class, Chatroulette_ConnectionInterface::class);
        self::assertInstanceOf(Evenement_EventEmitter::class, $mock);
    }
}
