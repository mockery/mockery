<?php

namespace test\Mockery\Matcher;

use Mockery\Matcher\HasValue;
use PHPUnit\Framework\TestCase;

class HasValueTest extends TestCase
{
    /** @test */
    public function it_can_handle_a_non_array()
    {
        $matcher = new HasValue(123);

        $actual = null;

        $this->assertFalse($matcher->match($actual));
    }
}
