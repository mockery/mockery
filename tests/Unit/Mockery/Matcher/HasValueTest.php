<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery\Matcher;

use ArrayObject;
use Mockery\Matcher\HasValue;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 */
final class HasValueTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testItCanHandleANonArray(): void
    {
        $matcher = new HasValue(123);

        $actual = null;

        self::assertFalse($matcher->match($actual));
    }

    /**
     * @throws Throwable
     */
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

    /**
     * @throws Throwable
     */
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
