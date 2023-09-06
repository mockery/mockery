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

namespace Mockery\Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class MockClassWithFinalWakeupTest extends MockeryTestCase
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
    public function testCreateMockForClassWithFinalWakeup()
    {
        $mock = $this->container->mock(TestWithFinalWakeup::class);
        $this->assertInstanceOf(TestWithFinalWakeup::class, $mock);
        $this->assertEquals(TestWithFinalWakeup::class .'::__wakeup', $mock->__wakeup());

        $mock = $this->container->mock(TestWithFinalWakeup::class);
        $this->assertInstanceOf(TestWithFinalWakeup::class, $mock);
        $this->assertEquals(TestWithFinalWakeup::class .'::__wakeup', $mock->__wakeup());
    }

    public function testCreateMockForClassWithNonFinalWakeup()
    {
        $mock = $this->container->mock(TestWithNonFinalWakeup::class);
        $this->assertInstanceOf(TestWithNonFinalWakeup::class, $mock);

        // Make sure __wakeup is overridden and doesn't return anything.
        $this->assertNull($mock->__wakeup());
    }
}

class TestWithFinalWakeup
{
    public function foo()
    {
        return 'foo';
    }

    public function bar()
    {
        return 'bar';
    }

    final public function __wakeup()
    {
        return __METHOD__;
    }
}

class SubclassWithFinalWakeup extends TestWithFinalWakeup
{
}

class TestWithNonFinalWakeup
{
    public function __wakeup()
    {
        return __METHOD__;
    }
}
