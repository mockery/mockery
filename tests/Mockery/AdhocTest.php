<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Ad-hoc unit tests for various scenarios reported by users
 */
class Mockery_AdhocTest extends MockeryTestCase
{

    public function setup()
    {
        $this->container = new \Mockery\Container(\Mockery::getDefaultGenerator(), \Mockery::getDefaultLoader());
    }

    public function teardown()
    {
        $this->container->mockery_close();
    }

    public function testSimplestMockCreation()
    {
        $m = $this->container->mock('MockeryTest_NameOfExistingClass');
        $this->assertTrue($m instanceof MockeryTest_NameOfExistingClass);
    }

    public function testMockeryInterfaceForClass()
    {
        $m = $this->container->mock('SplFileInfo');
        $this->assertTrue($m instanceof \Mockery\MockInterface);
    }

    public function testMockeryInterfaceForNonExistingClass()
    {
        $m = $this->container->mock('ABC_IDontExist');
        $this->assertTrue($m instanceof \Mockery\MockInterface);
    }

    public function testMockeryInterfaceForInterface()
    {
        $m = $this->container->mock('MockeryTest_NameOfInterface');
        $this->assertTrue($m instanceof \Mockery\MockInterface);
    }

    public function testMockeryInterfaceForAbstract()
    {
        $m = $this->container->mock('MockeryTest_NameOfAbstract');
        $this->assertTrue($m instanceof \Mockery\MockInterface);
    }

    public function testInvalidCountExceptionThrowsRuntimeExceptionOnIllegalComparativeSymbol()
    {
        $this->setExpectedException('Mockery\Exception\RuntimeException');
        $e = new \Mockery\Exception\InvalidCountException;
        $e->setExpectedCountComparative('X');
    }
}

class MockeryTest_NameOfExistingClass
{
}

interface MockeryTest_NameOfInterface
{
    public function foo();
}

abstract class MockeryTest_NameOfAbstract
{
    abstract public function foo();
}
