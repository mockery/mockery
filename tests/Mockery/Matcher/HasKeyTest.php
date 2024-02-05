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

    /** @test */
    public function it_matches_an_array()
    {
        $matcher = new HasKey('dave');

        $actual = [
            'foo' => 'bar',
            'dave' => 123,
            'bar' => 'baz',
        ];

        $this->assertTrue($matcher->match($actual));
    }

    /** @test */
    public function it_matches_an_array_like_object()
    {
        $matcher = new HasKey('dave');

        $actual = new \ArrayObject([
            'foo' => 'bar',
            'dave' => 123,
            'bar' => 'baz',
        ]);

        $this->assertTrue($matcher->match($actual));
    }
}
