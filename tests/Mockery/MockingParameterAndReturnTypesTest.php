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

declare(strict_types=1); // Use strict types to ensure exact types are returned or passed

namespace test\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class MockingParameterAndReturnTypesTest extends MockeryTestCase
{
    public function setup()
    {
        $this->container = new \Mockery\Container;
    }

    public function teardown()
    {
        $this->container->mockery_close();
    }

    public function testMockingStringReturnType()
    {
        $mock = $this->container->mock("test\Mockery\TestWithParameterAndReturnType");

        $mock->shouldReceive("returnString");
        $this->assertSame("", $mock->returnString());
    }

    public function testMockingIntegerReturnType()
    {
        $mock = $this->container->mock("test\Mockery\TestWithParameterAndReturnType");

        $mock->shouldReceive("returnInteger");
        $this->assertEquals(0, $mock->returnInteger());
    }

    public function testMockingFloatReturnType()
    {
        $mock = $this->container->mock("test\Mockery\TestWithParameterAndReturnType");

        $mock->shouldReceive("returnFloat");
        $this->assertSame(0.0, $mock->returnFloat());
    }

    public function testMockingBooleanReturnType()
    {
        $mock = $this->container->mock("test\Mockery\TestWithParameterAndReturnType");

        $mock->shouldReceive("returnBoolean");
        $this->assertSame(false, $mock->returnBoolean());
    }

    public function testMockingArrayReturnType()
    {
        $mock = $this->container->mock("test\Mockery\TestWithParameterAndReturnType");

        $mock->shouldReceive("returnArray");
        $this->assertSame([], $mock->returnArray());
    }

    public function testMockingGeneratorReturnTyps()
    {
        $mock = $this->container->mock("test\Mockery\TestWithParameterAndReturnType");

        $mock->shouldReceive("returnGenerator");
        $this->assertInstanceOf("\Generator", $mock->returnGenerator());
    }

    public function testMockingCallableReturnType()
    {
        $mock = $this->container->mock("test\Mockery\TestWithParameterAndReturnType");

        $mock->shouldReceive("returnCallable");
        $this->assertTrue(is_callable($mock->returnCallable()));
    }

    public function testMockingClassReturnTypes()
    {
        $mock = $this->container->mock("test\Mockery\TestWithParameterAndReturnType");

        $mock->shouldReceive("withClassReturnType");
        $this->assertInstanceOf("test\Mockery\TestWithParameterAndReturnType", $mock->withClassReturnType());
    }

    public function testMockingParameterTypes()
    {
        $mock = $this->container->mock("test\Mockery\TestWithParameterAndReturnType");

        $mock->shouldReceive("withScalarParameters");
        $mock->withScalarParameters(1, 1.0, true, 'string');
    }
}


abstract class TestWithParameterAndReturnType
{
    public function returnString(): string
    {
    }

    public function returnInteger(): int
    {
    }

    public function returnFloat(): float
    {
    }

    public function returnBoolean(): bool
    {
    }

    public function returnArray(): array
    {
    }

    public function returnCallable(): callable
    {
    }

    public function returnGenerator(): \Generator
    {
    }

    public function withClassReturnType(): TestWithParameterAndReturnType
    {
    }

    public function withScalarParameters(int $integer, float $float, bool $boolean, string $string)
    {
    }
}
