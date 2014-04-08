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
 * @copyright  Copyright (c) 2010-2014 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace test\Mockery;

class MockingProtectedMethodsTest extends \PHPUnit_Framework_TestCase
{

    public function setup ()
    {
        $this->container = new \Mockery\Container;
    }

    public function teardown()
    {
        $this->container->mockery_close();
    }

    /**
     * @test
     *
     * This is a regression test, basically we don't want the mock handling
     * interfering with calling protected methods partials
     */
    public function shouldAutomaticallyDeferCallsToProtectedMethodsForPartials()
    {
        $mock = $this->container->mock("test\Mockery\TestWithProtectedMethods[foo]");
        $this->assertEquals("bar", $mock->bar());
    }

    /**
     * @test
     *
     * This is a regression test, basically we don't want the mock handling
     * interfering with calling protected methods partials
     */
    public function shouldAutomaticallyDeferCallsToProtectedMethodsForRuntimePartials()
    {
        $mock = $this->container->mock("test\Mockery\TestWithProtectedMethods")->shouldDeferMissing();
        $this->assertEquals("bar", $mock->bar());
    }

    /** @test */
    public function shouldAutomaticallyIgnoreAbstractProtectedMethods()
    {
        $mock = $this->container->mock("test\Mockery\TestWithProtectedMethods")->shouldDeferMissing();
        $this->assertEquals(null, $mock->foo());
    }

    /** @test */
    public function shouldAllowMockingProtectedMethods()
    {
        $mock = $this->container->mock("test\Mockery\TestWithProtectedMethods")
            ->shouldDeferMissing()
            ->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive("protectedBar")->andReturn("notbar");
        $this->assertEquals("notbar", $mock->bar());
    }

    /** @test */
    public function shouldAllowMockingProtectedMethodOnDefinitionTimePartial()
    {
        $mock = $this->container->mock("test\Mockery\TestWithProtectedMethods[protectedBar]")
            ->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive("protectedBar")->andReturn("notbar");
        $this->assertEquals("notbar", $mock->bar());
    }

    /** @test */
    public function shouldAllowMockingAbstractProtectedMethods()
    {
        $mock = $this->container->mock("test\Mockery\TestWithProtectedMethods")
            ->shouldDeferMissing()
            ->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive("abstractProtected")->andReturn("abstractProtected");
        $this->assertEquals("abstractProtected", $mock->foo());
    }
}


abstract class TestWithProtectedMethods
{
    public function foo()
    {
        return $this->abstractProtected();
    }

    abstract protected function abstractProtected();

    public function bar()
    {
        return $this->protectedBar();
    }

    protected function protectedBar()
    {
        return 'bar';
    }
}

