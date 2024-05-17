<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Matcher;

use ArrayObject;
use Mockery\Matcher\HasValue;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Mockery
 */
final class HasValueTest extends TestCase
{
    public function testItCanHandleANonArray(): void
    {
        $matcher = new HasValue(123);

        $actual = null;

        self::assertFalse($matcher->match($actual));
    }

    public function testItMatchesAnArray(): void
    {
        $matcher = new HasValue(123);

        $actual = [
            'foo' => 'bar',
            'dave' => 123,
            'bar' => 'baz',
        ];

        self::assertTrue($matcher->match($actual));
    }

    public function testItMatchesAnArrayLikeObject(): void
    {
        $matcher = new HasValue(123);

        $actual = new ArrayObject([
            'foo' => 'bar',
            'dave' => 123,
            'bar' => 'baz',
        ]);

        self::assertTrue($matcher->match($actual));
    }
}
