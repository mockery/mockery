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

use Mockery\Generator;

class GeneratorTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function shouldNotDuplicateDoublyInheritedMethods()
    {
        $classes = array('Evenement_EventEmitter', 'Chatroulette_ConnectionInterface');
        $definition = Generator::createClassMockCode($classes, 'Chatroulette_Connection_Mock');
        $expected = file_get_contents(__DIR__.'/Fixtures/chatroulette_connection_mock.txt');

        $this->assertSame($expected, $definition);
    }
}

interface Evenement_EventEmitterInterface
{
    public function on($name, $callback);
}

class Evenement_EventEmitter implements Evenement_EventEmitterInterface
{
    public function on($name, $callback)
    {
    }
}

interface React_StreamInterface extends Evenement_EventEmitterInterface
{
    public function close();
}

interface React_ReadableStreamInterface extends React_StreamInterface
{
    public function pause();
}

interface React_WritableStreamInterface extends React_StreamInterface
{
    public function write($data);
}

interface Chatroulette_ConnectionInterface
    extends React_ReadableStreamInterface,
            React_WritableStreamInterface
{
}
