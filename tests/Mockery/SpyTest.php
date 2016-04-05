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

namespace test\Mockery;

use Mockery as m;
use Mockery\Spy;

class SpyTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->container = new \Mockery\Container;
    }

    public function teardown()
    {
        $this->container->mockery_close();
    }

    /** @test */
    public function itVerifiesAMethodWasCalled()
    {
        $spy = m::spy();
        $spy->myMethod();
        $spy->shouldHaveReceived("myMethod");

        $this->setExpectedException("Mockery\Exception\InvalidCountException");
        $spy->shouldHaveReceived("someMethodThatWasNotCalled");
    }

    /** @test */
    public function itVerifiesAMethodWasNotCalled()
    {
        $spy = m::spy();
        $spy->shouldNotHaveReceived("myMethod");

        $this->setExpectedException("Mockery\Exception\InvalidCountException");
        $spy->myMethod();
        $spy->shouldNotHaveReceived("myMethod");
    }

    /** @test */
    public function itVerifiesAMethodWasNotCalledWithParticularArguments()
    {
        $spy = m::spy();
        $spy->myMethod(123, 456);

        $spy->shouldNotHaveReceived("myMethod", array(789, 10));

        $this->setExpectedException("Mockery\Exception\InvalidCountException");
        $spy->shouldNotHaveReceived("myMethod", array(123, 456));
    }

    /** @test */
    public function itVerifiesAMethodWasCalledASpecificNumberOfTimes()
    {
        $spy = m::spy();
        $spy->myMethod();
        $spy->myMethod();
        $spy->shouldHaveReceived("myMethod")->twice();

        $this->setExpectedException("Mockery\Exception\InvalidCountException");
        $spy->myMethod();
        $spy->shouldHaveReceived("myMethod")->twice();
    }

    /** @test */
    public function itVerifiesAMethodWasCalledWithSpecificArguments()
    {
        $spy = m::spy();
        $spy->myMethod(123, "a string");
        $spy->shouldHaveReceived("myMethod")->with(123, "a string");
        $spy->shouldHaveReceived("myMethod", array(123, "a string"));

        $this->setExpectedException("Mockery\Exception\InvalidCountException");
        $spy->shouldHaveReceived("myMethod")->with(123);
    }

    /** @test */
    public function itIncrementsExpectationCountWhenShouldHaveReceivedIsUsed()
    {
        $spy = m::spy();
        $spy->myMethod('param1', 'param2');
        $spy->shouldHaveReceived('myMethod')->with('param1', 'param2');
        $this->assertEquals(1, $spy->mockery_getExpectationCount());
    }

    /** @test */
    public function itIncrementsExpectationCountWhenShouldNotHaveReceivedIsUsed()
    {
        $spy = m::spy();
        $spy->shouldNotHaveReceived('method');
        $this->assertEquals(1, $spy->mockery_getExpectationCount());
    }
}
