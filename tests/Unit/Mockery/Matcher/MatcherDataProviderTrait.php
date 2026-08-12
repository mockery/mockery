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

use stdClass;

trait MatcherDataProviderTrait
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
}
