<?php

namespace Mockery;

use Mockery as m;
use Mockery\Fixtures\SemiReservedWordsAsMethods;

/**
 * @requires PHP 7.0.0
 */
class MockeryCanMockClassesWithSemiReservedWordsTest extends \PHPUnit_Framework_TestCase
{
    /** 
     * @test 
     */
    public function smoke_test()
    {
        require __DIR__.'/Fixtures/SemiReservedWordsAsMethods.php';

        $mock = m::mock("Mockery\Fixtures\SemiReservedWordsAsMethods");

        $mock->shouldReceive("include")->andReturn("foo");

        $this->assertTrue(method_exists($mock, "include"));
        $this->assertEquals("foo", $mock->include());
    }
}
