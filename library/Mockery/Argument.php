<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery;

use Closure;
use Deprecated;
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

/**
 * A static Factory for creating argument matchers.
 *
 * @see \Tests\Unit\Mockery\ArgumentTest
 */
final class Argument
{
    public static function andAnyOtherArgs(): AndAnyOtherArgs
    {
        return new AndAnyOtherArgs();
    }

    public static function andAnyOthers(): AndAnyOtherArgs
    {
        return new AndAnyOtherArgs();
    }

    public static function any(): Any
    {
        return new Any();
    }

    public static function anyArgs(): AnyArgs
    {
        return new AnyArgs();
    }

    /**
     * @param mixed ...$expected
     */
    public static function anyOf(...$expected): AnyOf
    {
        return new AnyOf($expected);
    }

    /**
     * @param mixed $reference
     */
    public static function capture(&$reference): ClosureMatcher
    {
        $closure = static function ($argument) use (&$reference): bool {
            $reference = $argument;

            return true;
        };

        return new ClosureMatcher($closure);
    }

    /**
     * @param mixed ...$expected
     */
    public static function contains(...$expected): Contains
    {
        return new Contains($expected);
    }

    /**
     * @param mixed ...$expected
     */
    public static function ducktype(string ...$expected): Ducktype
    {
        return new Ducktype($expected);
    }

    /**
     * @param mixed $expected
     */
    public static function hasKey($expected): HasKey
    {
        return new HasKey($expected);
    }

    /**
     * @param mixed $expected
     */
    public static function hasValue($expected): HasValue
    {
        return new HasValue($expected);
    }

    /**
     * @param mixed $expected
     */
    public static function isEqual($expected): IsEqual
    {
        return new IsEqual($expected);
    }

    /**
     * @param mixed $expected
     */
    public static function isSame($expected): IsSame
    {
        return new IsSame($expected);
    }

    public static function multiArgumentClosure(Closure $expected): MultiArgumentClosure
    {
        return new MultiArgumentClosure($expected);
    }

    /**
     * @deprecated 1.6.16 Use `Mockery\Argument::isEqual()` or `Mockery\Argument::isSame()` instead.
     *
     * @param mixed $expected
     */
    #[Deprecated('Use `Mockery\Argument::isEqual()` or `Mockery\Argument::isSame()` instead.', '1.6.16')]
    public static function mustBe($expected): MustBe
    {
        return new MustBe($expected);
    }

    public static function noArgs(): NoArgs
    {
        return new NoArgs();
    }

    /**
     * @param mixed $expected
     */
    public static function not($expected): Not
    {
        return new Not($expected);
    }

    /**
     * @param mixed ...$expected
     */
    public static function notAnyOf(...$expected): NotAnyOf
    {
        return new NotAnyOf($expected);
    }

    /**
     * @param Closure(mixed):bool $expected
     */
    public static function on(Closure $expected): ClosureMatcher
    {
        return new ClosureMatcher($expected);
    }

    public static function pattern(string $expected): Pattern
    {
        return new Pattern($expected);
    }

    /**
     * @param array<mixed> $expected
     */
    public static function subset(array $expected, bool $strict = true): Subset
    {
        return new Subset($expected, $strict);
    }

    public static function type(string $expected): Type
    {
        return new Type($expected);
    }
}
