<?php

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
}
