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
 * @copyright  Copyright (c) 2012 Philip Graham <philip.robert.graham@gmail.com>
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace test\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class MockClassWithFinalToStringTest extends MockeryTestCase
{
    protected $container;

    protected function mockeryTestSetUp()
    {
        $this->container = new \Mockery\Container();
    }

    protected function mockeryTestTearDown()
    {
        $this->container->mockery_close();
    }

    /**
     * @test
     *
     * Test that we are able to create partial mocks of classes that have
     * a __wakeup method marked as final. As long as __wakeup is not one of the
     * mocked methods.
     */
    public function testCreateMockForClassWithFinalToString()
    {
        $mock = $this->container->mock("test\Mockery\TestWithFinalToString");
        $this->assertInstanceOf("test\Mockery\TestWithFinalToString", $mock);
        $this->assertEquals('test\Mockery\TestWithFinalToString::__toString', $mock->__toString());

        $mock = $this->container->mock('test\Mockery\SubclassWithFinalToString');
        $this->assertInstanceOf('test\Mockery\TestWithFinalToString', $mock);
        $this->assertEquals('test\Mockery\TestWithFinalToString::__toString', $mock->__toString());
    }

    public function testCreateMockForClassWithNonFinalToString()
    {
        $mock = $this->container->mock('test\Mockery\TestWithNonFinalToString');
        $this->assertInstanceOf('test\Mockery\TestWithNonFinalToString', $mock);

        // Make sure __toString is overridden.
        $this->assertNotEquals('bar', $mock->__toString());
    }
}

class TestWithFinalToString
{
    public function foo()
    {
        return 'foo';
    }

    public function bar()
    {
        return 'bar';
    }

    final public function __toString()
    {
        return __METHOD__;
    }
}

class SubclassWithFinalToString extends TestWithFinalToString
{
}

class TestWithNonFinalToString
{
    public function __toString()
    {
        return 'bar';
    }
}
