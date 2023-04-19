<?php

namespace MockeryTest\Unit\Mockery;

use MockeryTest\Mockery\Fixtures\SemiReservedWordsAsMethods;

class MockeryCanMockClassesWithSemiReservedWordsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function smoke_test()
    {
        $mock = \Mockery::mock(\MockeryTest\Fixture\SemiReservedWordsAsMethods::class);

        $mock->shouldReceive("include")->andReturn("foo");

        $this->assertTrue(\method_exists($mock, "include"));
        $this->assertEquals("foo", $mock->include());
    }
}
