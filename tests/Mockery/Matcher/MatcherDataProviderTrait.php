<?php

namespace test\Mockery\Matcher;

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
