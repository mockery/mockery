<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Matcher;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Matcher\Subset;

/**
 * @coversDefaultClass \Mockery
 */
final class SubsetTest extends MockeryTestCase
{
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

    public function testItReturnsFalseIfActualIsNotAnArray(): void
    {
        $matcher = new Subset([
            'dave' => 123,
        ]);

        $actual = null;

        self::assertFalse($matcher->match($actual));
    }
}
