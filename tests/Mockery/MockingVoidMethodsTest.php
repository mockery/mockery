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

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @requires PHP 7.1.0RC3
 */
class MockingVoidMethodsTest extends MockeryTestCase
{
    protected function setUp()
    {
        require_once __DIR__."/Fixtures/MethodWithVoidReturnType.php";

        $this->container = new \Mockery\Container;
    }

    protected function tearDown()
    {
        $this->container->mockery_close();
    }

    /** @test */
    public function itShouldSuccessfullyBuildTheMock()
    {
        $mock = $this->container->mock("test\Mockery\Fixtures\MethodWithVoidReturnType");

        $this->assertTrue($mock instanceof \test\Mockery\Fixtures\MethodWithVoidReturnType);
    }

    /** @test */
    public function it_can_stub_and_mock_void_methods()
    {
        $mock = $this->container->mock("test\Mockery\Fixtures\MethodWithVoidReturnType");

        $mock->shouldReceive("foo");
        $mock->foo();
    }
}
