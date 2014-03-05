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

/**
 */
class Mockery_MockTest extends PHPUnit_Framework_TestCase
{

    public function setup ()
    {
        $this->container = new \Mockery\Container(\Mockery::getDefaultGenerator(), \Mockery::getDefaultLoader());
    }

    public function teardown()
    {
        $this->container->mockery_close();
    }

    public function testAnonymousMockWorksWithNotAllowingMockingOfNonExistantMethods()
    {
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $m = $this->container->mock();
        $m->shouldReceive("test123")->andReturn(true);
        assertThat($m->test123(), equalTo(true));
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }

    public function testMockWithNotAllowingMockingOfNonExistantMethodsCanBeGivenAdditionalMethodsToMockEvenIfTheyDontExistOnClass()
    {
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        $m = $this->container->mock('ExampleClassForTestingNonExistentMethod');
        $m->shouldAllowMockingMethod('testSomeNonExistentMethod');
        $m->shouldReceive("testSomeNonExistentMethod")->andReturn(true);
        assertThat($m->testSomeNonExistentMethod(), equalTo(true));
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(true);
    }
}


class ExampleClassForTestingNonExistentMethod
{
}

