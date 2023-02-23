<?php

namespace Mockery;

use Mockery as m;
use Mockery\Fixtures\SemiReservedWordsAsMethods;
use PHPUnit\Framework\TestCase;

class MockeryCanMockClassesWithSemiReservedWordsTest extends TestCase
{
    /**
     * @test
     */
    public function smoke_test()
    {
        require_once __DIR__ . '/Fixtures/SemiReservedWordsAsMethods.php';

        $mock = m::mock("Mockery\Fixtures\SemiReservedWordsAsMethods");

        $mock->shouldReceive("include")->andReturn("foo")->once();

        $this->assertTrue(method_exists($mock, "include"));
        $this->assertEquals("foo", $mock->include());
    }
}
