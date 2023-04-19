<?php

namespace MockeryTest\Unit\Mockery;

class ProxyMockingTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @test */
    public function finalClassCannotBeMocked()
    {
        $this->expectException(\Mockery\Exception::class);

        \mock(\MockeryTest\Fixture\UnmockableClass::class);
    }

    /** @test */
    public function passesThruAnyMethod()
    {
        $mock = \mock(new \MockeryTest\Fixture\UnmockableClass());

        $this->assertSame(1, $mock->anyMethod());
    }

    /** @test */
    public function passesThruVirtualMethods()
    {
        $mock = \mock(new \MockeryTest\Fixture\UnmockableClass());

        $this->assertSame(42, $mock->theAnswer());
    }
}
