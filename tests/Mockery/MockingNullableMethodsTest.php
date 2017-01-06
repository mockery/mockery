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
use Mockery\Generator\Method;
use test\Mockery\Fixtures\MethodWithNullableReturnType;

/**
 * @requires PHP 7.1.0RC3
 */
class MockingNullableMethodsTest extends MockeryTestCase
{
    /**
     * @var \Mockery\Container
     */
    private $container;

    protected function setUp()
    {
        require_once __DIR__."/Fixtures/MethodWithNullableReturnType.php";

        $this->container = new \Mockery\Container;
    }

    protected function tearDown()
    {
        $this->container->mockery_close();
    }

    /**
     * @test
     */
    public function itShouldAllowNonNullableTypeToBeSet()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nonNullablePrimitive')->andReturn('a string');
        $mock->nonNullablePrimitive();
    }

    /**
     * @test
     * @expectedException \TypeError
     */
    public function itShouldNotAllowNonNullToBeNull()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nonNullablePrimitive')->andReturn(null);
        $mock->nonNullablePrimitive();
    }

    /**
     * @test
     */
    public function itShouldAllowPrimitiveNullableToBeNull()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nullablePrimitive')->andReturn(null);
        $mock->nullablePrimitive();
    }

    /**
     * @test
     */
    public function itShouldAllowPrimitiveNullabeToBeSet()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nullablePrimitive')->andReturn('a string');
        $mock->nullablePrimitive();
    }

    /**
     * @test
     */
    public function itShouldAllowSelfToBeSet()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nonNullableSelf')->andReturn(new MethodWithNullableReturnType());
        $mock->nonNullableSelf();
    }

    /**
     * @test
     * @expectedException \TypeError
     */
    public function itShouldNotAllowSelfToBeNull()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nonNullableSelf')->andReturn(null);
        $mock->nonNullableSelf();
    }

    /**
     * @test
     */
    public function itShouldAllowNullableSelfToBeSet()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nullableSelf')->andReturn(new MethodWithNullableReturnType());
        $mock->nullableSelf();
    }

    /**
     * @test
     */
    public function itShouldAllowNullableSelfToBeNull()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nullableSelf')->andReturn(null);
        $mock->nullableSelf();
    }

    /**
     * @test
     */
    public function itShouldAllowClassToBeSet()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nonNullableClass')->andReturn(new MethodWithNullableReturnType());
        $mock->nonNullableClass();
    }

    /**
     * @test
     * @expectedException \TypeError
     */
    public function itShouldNotAllowClassToBeNull()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nonNullableClass')->andReturn(null);
        $mock->nonNullableClass();
    }

    /**
     * @test
     */
    public function itShouldAllowNullalbeClassToBeSet()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nullableClass')->andReturn(new MethodWithNullableReturnType());
        $mock->nullableClass();
    }

    /**
     * @test
     */
    public function itShouldAllowNullableClassToBeNull()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableReturnType');

        $mock->shouldReceive('nullableClass')->andReturn(null);
        $mock->nullableClass();
    }
}
