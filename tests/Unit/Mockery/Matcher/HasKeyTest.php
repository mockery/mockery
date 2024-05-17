<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Matcher;

use ArrayObject;
use Mockery\Matcher\HasKey;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Mockery
 */
final class HasKeyTest extends TestCase
{
    public function testItCanHandleANonArray(): void
    {
        $matcher = new HasKey('dave');

        $actual = null;

        self::assertFalse($matcher->match($actual));
    }

    public function testItMatchesAnArray(): void
    {
        $matcher = new HasKey('dave');

        $actual = [
            'foo' => 'bar',
            'dave' => 123,
            'bar' => 'baz',
        ];

        self::assertTrue($matcher->match($actual));
    }

    public function testItMatchesAnArrayLikeObject(): void
    {
        $matcher = new HasKey('dave');

        $actual = new ArrayObject([
            'foo' => 'bar',
            'dave' => 123,
            'bar' => 'baz',
        ]);

        self::assertTrue($matcher->match($actual));
    }
}
