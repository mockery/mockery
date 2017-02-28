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
use test\Mockery\Fixtures\MethodWithNullableReturnType;

/**
 * @requires PHP 7.1.0RC3
 */
class MockingMethodsWithNullableParametersTest extends MockeryTestCase
{
    /**
     * @var \Mockery\Container
     */
    private $container;

    protected function setUp()
    {
        require_once __DIR__."/Fixtures/MethodWithNullableParameters.php";

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
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nonNullablePrimitive')->with('a string');
        $mock->nonNullablePrimitive('a string');
    }

    /**
     * @test
     * @expectedException \TypeError
     */
    public function itShouldNotAllowNonNullToBeNull()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->nonNullablePrimitive(null);
    }

    /**
     * @test
     */
    public function itShouldAllowPrimitiveNullableToBeNull()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nullablePrimitive')->with(null);
        $mock->nullablePrimitive(null);
    }

    /**
     * @test
     */
    public function itShouldAllowPrimitiveNullabeToBeSet()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nullablePrimitive')->with('a string');
        $mock->nullablePrimitive('a string');
    }
    /**
     * @test
     */
    public function itShouldAllowSelfToBeSet()
    {
        $obj = new \test\Mockery\Fixtures\MethodWithNullableParameters;
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nonNullableSelf')->with($obj);
        $mock->nonNullableSelf($obj);
    }

    /**
     * @test
     * @expectedException \TypeError
     */
    public function itShouldNotAllowSelfToBeNull()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->nonNullableSelf(null);
    }

    /**
     * @test
     */
    public function itShouldAllowNullalbeSelfToBeSet()
    {
        $obj = new \test\Mockery\Fixtures\MethodWithNullableParameters;

        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nullableSelf')->with($obj);
        $mock->nullableSelf($obj);
    }

    /**
     * @test
     */
    public function itShouldAllowNullableSelfToBeNull()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nullableClass')->with(null);
        $mock->nullableClass(null);
    }

    /**
     * @test
     */
    public function itShouldAllowClassToBeSet()
    {
        $obj = new \test\Mockery\Fixtures\MethodWithNullableParameters;
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nonNullableClass')->with($obj);
        $mock->nonNullableClass($obj);
    }

    /**
     * @test
     * @expectedException \TypeError
     */
    public function itShouldNotAllowClassToBeNull()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->nonNullableClass(null);
    }

    /**
     * @test
     */
    public function itShouldAllowNullalbeClassToBeSet()
    {
        $obj = new \test\Mockery\Fixtures\MethodWithNullableParameters;

        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nullableClass')->with($obj);
        $mock->nullableClass($obj);
    }

    /**
     * @test
     */
    public function itShouldAllowNullableClassToBeNull()
    {
        $mock = $this->container->mock('test\Mockery\Fixtures\MethodWithNullableParameters');

        $mock->shouldReceive('nullableClass')->with(null);
        $mock->nullableClass(null);
    }
}
