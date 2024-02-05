<?php

namespace test\Mockery\Matcher;

use Mockery\Matcher\HasKey;
use PHPUnit\Framework\TestCase;

class HasKeyTest extends TestCase
{
    /** @test */
    public function it_can_handle_a_non_array()
    {
        $matcher = new HasKey('dave');

        $actual = null;

        $this->assertFalse($matcher->match($actual));
    }
}
