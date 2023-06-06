<?php

namespace test\Mockery\Matcher;

use stdClass;

trait MatcherDataProviderTrait
{
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
