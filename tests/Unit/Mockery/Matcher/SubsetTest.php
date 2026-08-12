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

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Matcher\Subset;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 */
final class SubsetTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testItCanRunALooseComparison(): void
    {
        $matcher = Subset::loose([
            'dave' => 123,
        ]);

        $actual = [
            'foo' => 'bar',
            'dave' => 123.0,
            'bar' => 'baz',
        ];

        self::assertTrue($matcher->match($actual));
    }

    /**
     * @throws Throwable
     */
    public function testItCorrectlyFormatsNestedArraysIntoAString(): void
    {
        $expected = [
            'foo' => 123,
            'bar' => [
                'baz' => 456,
            ],
        ];

        $matcher = new Subset($expected);
        $actual = $matcher->__toString();

        $tests = [
            '/foo=123/',
            '/bar=\[[^[\]]+\]/', // e.g. bar=[<anything other than square brackets>]
            '/baz=456/',
        ];

        foreach ($tests as $pattern) {
            self::assertMatchesRegularExpression($pattern, $actual);
        }
    }

    /**
     * @throws Throwable
     */
    public function testItIsStrictByDefault(): void
    {
        $matcher = new Subset([
            'dave' => 123,
        ]);

        $actual = [
            'foo' => 'bar',
            'dave' => 123.0,
            'bar' => 'baz',
        ];

        self::assertFalse($matcher->match($actual));
    }

    /**
     * @throws Throwable
     */
    public function testItMatchesAShallowSubset(): void
    {
        $matcher = Subset::strict([
            'dave' => 123,
        ]);

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
    public function testItRecursivelyMatches(): void
    {
        $matcher = Subset::strict([
            'foo' => [
                'bar' => [
                    'baz' => 123,
                ],
            ],
        ]);

        $actual = [
            'foo' => [
                'bar' => [
                    'baz' => 123,
                ],
            ],
            'dave' => 123,
            'bar' => 'baz',
        ];

        self::assertTrue($matcher->match($actual));
    }

    /**
     * @throws Throwable
     */
    public function testItReturnsFalseIfActualIsNotAnArray(): void
    {
        $matcher = new Subset([
            'dave' => 123,
        ]);

        $actual = null;

        self::assertFalse($matcher->match($actual));
    }
}
