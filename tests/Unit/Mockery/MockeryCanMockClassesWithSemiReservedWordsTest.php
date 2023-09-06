<?php

namespace Mockery;

use Fixtures\SemiReservedWordsAsMethods;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class MockeryCanMockClassesWithSemiReservedWordsTest extends TestCase
{
    /**
     * @test
     */
    public function smoke_test()
    {
        $mock = m::mock(SemiReservedWordsAsMethods::class);

        $mock->shouldReceive("include")->andReturn("foo");

        $this->assertTrue(method_exists($mock, "include"));
        $this->assertEquals("foo", $mock->include());
    }
}
