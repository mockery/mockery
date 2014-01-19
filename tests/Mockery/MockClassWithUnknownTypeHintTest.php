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
 */

namespace test\Mockery;

class MockClassWithUnknownTypeHintTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp() {
        $this->container = new \Mockery\Container;
    }

    protected function tearDown() {
        $this->container->mockery_close();
    }

    /** @test */
    public function itShouldSuccessfullyBuildTheMock()
    {
        $this->container->mock("test\Mockery\HasUnknownClassAsTypeHintOnMethod");
    }

}

class HasUnknownClassAsTypeHintOnMethod
{
    public function foo(\UnknownTestClass\Bar $bar)
    {

    }
}
