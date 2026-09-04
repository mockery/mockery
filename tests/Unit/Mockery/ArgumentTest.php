<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery;

use ArrayObject;
use Mockery\Argument;
use Mockery\Matcher\AndAnyOtherArgs;
use Mockery\Matcher\Any;
use Mockery\Matcher\AnyArgs;
use Mockery\Matcher\AnyOf;
use Mockery\Matcher\Closure as ClosureMatcher;
use Mockery\Matcher\Contains;
use Mockery\Matcher\Ducktype;
use Mockery\Matcher\HasKey;
use Mockery\Matcher\HasValue;
use Mockery\Matcher\IsEqual;
use Mockery\Matcher\IsSame;
use Mockery\Matcher\MultiArgumentClosure;
use Mockery\Matcher\MustBe;
use Mockery\Matcher\NoArgs;
use Mockery\Matcher\Not;
use Mockery\Matcher\NotAnyOf;
use Mockery\Matcher\Pattern;
use Mockery\Matcher\Subset;
use Mockery\Matcher\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Tests\Unit\AbstractTestCase;
use Throwable;

/**
 * @covers \Mockery
 * @covers \Mockery\Argument
 * @covers \Mockery\Container
 * @covers \Mockery\Generator\CachingGenerator
 * @covers \Mockery\Generator\StringManipulationGenerator
 * @covers \Mockery\Matcher\AndAnyOtherArgs
 * @covers \Mockery\Matcher\Any
 * @covers \Mockery\Matcher\AnyArgs
 * @covers \Mockery\Matcher\AnyOf
 * @covers \Mockery\Matcher\Closure
 * @covers \Mockery\Matcher\Contains
 * @covers \Mockery\Matcher\Ducktype
 * @covers \Mockery\Matcher\HasKey
 * @covers \Mockery\Matcher\HasValue
 * @covers \Mockery\Matcher\IsEqual
 * @covers \Mockery\Matcher\IsSame
 * @covers \Mockery\Matcher\MatcherAbstract
 * @covers \Mockery\Matcher\MultiArgumentClosure
 * @covers \Mockery\Matcher\MustBe
 * @covers \Mockery\Matcher\NoArgs
 * @covers \Mockery\Matcher\Not
 * @covers \Mockery\Matcher\NotAnyOf
 * @covers \Mockery\Matcher\Pattern
 * @covers \Mockery\Matcher\Subset
 * @covers \Mockery\Matcher\Type
 */
final class ArgumentTest extends AbstractTestCase
{
    public static function isEqualDataProvider(): iterable
    {
        yield from self::isSameDataProvider();

        yield from [
            'bool-int-1' => [true, 1],
            'bool-int-0' => [false, 0],
            'bool-float-1' => [true, 1.0],
            'bool-float-0' => [false, 0.0],
            'int-string' => [42, '42'],
            'int-float' => [42, 42.0],
            'float-int' => [42.0, 42],
            'int-string-float' => [42, '42.0'],
            'float-string-int' => [42.0, '42'],
            'null-empty-string' => [null, ''],
            'object-different' => [new stdClass(), new stdClass()],
        ];
    }

    public static function isSameDataProvider(): iterable
    {
        $object = new stdClass();

        return [
            'string' => ['#BlackLivesMatter', '#BlackLivesMatter'],
            'bool-true' => [true, true],
            'bool-false' => [false, false],
            'int' => [42, 42],
            'float' => [2.0, 2.0],
            'null' => [null, null],
            'object' => [$object, $object],
        ];
    }

    public function testArgumentMatcher(): void
    {
        self::assertInstanceOf(AndAnyOtherArgs::class, Argument::andAnyOtherArgs());
        self::assertInstanceOf(AndAnyOtherArgs::class, Argument::andAnyOthers());
        self::assertInstanceOf(Any::class, Argument::any());
        self::assertInstanceOf(AnyOf::class, Argument::anyOf('foo', 'bar'));
        self::assertInstanceOf(ClosureMatcher::class, Argument::on(function () {}));
        self::assertInstanceOf(Contains::class, Argument::contains('foo'));
        self::assertInstanceOf(Ducktype::class, Argument::ducktype('foo'));
        self::assertInstanceOf(HasKey::class, Argument::hasKey('foo'));
        self::assertInstanceOf(HasValue::class, Argument::hasValue('foo'));
        self::assertInstanceOf(IsEqual::class, Argument::isEqual('foo'));
        self::assertInstanceOf(IsSame::class, Argument::isSame('foo'));
        self::assertInstanceOf(MustBe::class, Argument::mustBe('foo'));
        self::assertInstanceOf(Not::class, Argument::not('foo'));
        self::assertInstanceOf(NotAnyOf::class, Argument::notAnyOf('foo', 'bar'));
        self::assertInstanceOf(Pattern::class, Argument::pattern('#foo#'));
        self::assertInstanceOf(Subset::class, Argument::subset([
            'foo' => 'bar',
        ]));
        self::assertInstanceOf(Type::class, Argument::type('string'));
        self::assertInstanceOf(AnyArgs::class, Argument::anyArgs());
        self::assertInstanceOf(NoArgs::class, Argument::noArgs());
        self::assertInstanceOf(MultiArgumentClosure::class, Argument::multiArgumentClosure(function () {}));
    }

    /**
     * @throws Throwable
     */
    public function testHasKeyCanHandleANonArray(): void
    {
        $matcher = Argument::hasKey('dave');

        $actual = null;

        self::assertFalse($matcher->match($actual));
    }

    /**
     * @throws Throwable
     */
    public function testHasKeyMatchesAnArray(): void
    {
        $matcher = Argument::hasKey('dave');

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
    public function testHasKeyMatchesAnArrayLikeObject(): void
    {
        $matcher = Argument::hasKey('dave');

        $actual = new ArrayObject([
            'foo' => 'bar',
            'dave' => 123,
            'bar' => 'baz',
        ]);

        self::assertTrue($matcher->match($actual));
    }

    /**
     * @throws Throwable
     */
    public function testHasValueCanHandleANonArray(): void
    {
        $matcher = Argument::hasValue(123);

        $actual = null;

        self::assertFalse($matcher->match($actual));
    }

    /**
     * @throws Throwable
     */
    public function testHasValueMatchesAnArray(): void
    {
        $matcher = Argument::hasValue(123);

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
    public function testHasValueMatchesAnArrayLikeObject(): void
    {
        $matcher = Argument::hasValue(123);

        $actual = new ArrayObject([
            'foo' => 'bar',
            'dave' => 123,
            'bar' => 'baz',
        ]);

        self::assertTrue($matcher->match($actual));
    }

    /**
     * @dataProvider isEqualDataProvider
     *
     * @throws Throwable
     */
    #[DataProvider('isEqualDataProvider')]
    public function testIsEqual($expected, $actual): void
    {
        self::assertTrue(Argument::isEqual($expected)->match($actual));
    }

    /**
     * @dataProvider isSameDataProvider
     *
     * @throws Throwable
     */
    #[DataProvider('isSameDataProvider')]
    public function testIsSame($expected, $actual): void
    {
        self::assertTrue(Argument::isSame($expected)->match($actual));
    }

    /**
     * @throws Throwable
     */
    public function testSubsetCanCorrectlyFormatsNestedArraysIntoAString(): void
    {
        $expected = [
            'foo' => 123,
            'bar' => [
                'baz' => 456,
            ],
        ];

        $actual = Argument::subset($expected)->__toString();

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
    public function testSubsetCanRunALooseComparison(): void
    {
        $matcher = Argument::subset([
            'dave' => 123,
        ], false);

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
    public function testSubsetIsStrictByDefault(): void
    {
        $matcher = Argument::subset([
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
    public function testSubsetMatchesAShallowSubset(): void
    {
        $matcher = Argument::subset([
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
    public function testSubsetRecursivelyMatches(): void
    {
        $matcher = Argument::subset([
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
    public function testSubsetReturnsFalseIfActualIsNotAnArray(): void
    {
        $matcher = Argument::subset([
            'dave' => 123,
        ]);

        $actual = null;

        self::assertFalse($matcher->match($actual));
    }
}
