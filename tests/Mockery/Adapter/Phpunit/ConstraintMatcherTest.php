<?php

namespace test\Mockery\Adapter\Phpunit;

use Mockery;
use Mockery\Adapter\Phpunit\ConstraintMatcher;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsNull;
use PHPUnit\Framework\Constraint\LogicalOr;

class ConstraintMatcherTest extends MockeryTestCase
{

    /** @dataProvider matchDataProvider */
    public function testMatch(Constraint $constraint, bool $expectedResult, $actual)
    {
        self::assertSame($expectedResult, (new ConstraintMatcher($constraint))->match($actual));
    }

    public static function matchDataProvider(): iterable
    {
        yield from [
            'is null constraint matches null' => [new IsNull(), true, null],
            'is null constraint does not match 123' => [new IsNull(), false, 123],
            'complex constraint matches null' => [LogicalOr::fromConstraints(new IsNull(), new IsIdentical('test')), true, null],
            'complex constraint matches test' => [LogicalOr::fromConstraints(new IsNull(), new IsIdentical('test')), true, 'test'],
            'complex constraint does not match 123' => [LogicalOr::fromConstraints(new IsNull(), new IsIdentical('test')), false, 123],
        ];
    }

    /** @dataProvider toStringDataProvider */
    public function testToString(Constraint $constraint, string $expectedResult)
    {
        self::assertSame($expectedResult, (string) new ConstraintMatcher($constraint));
    }

    public static function toStringDataProvider(): iterable
    {
        yield from [
            'is null' => [new IsNull(), '<PhpUnitConstraint[is null]>'],
            'complex' => [LogicalOr::fromConstraints(new IsNull(), new IsIdentical('test')), '<PhpUnitConstraint[is null or is identical to \'test\']>'],
        ];
    }
}
