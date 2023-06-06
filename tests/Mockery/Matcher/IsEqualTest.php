<?php

namespace test\Mockery\Matcher;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Matcher\IsEqual;

class IsEqualTest extends MockeryTestCase
{
    use MatcherDataProviderTrait;

    /** @dataProvider isEqualDataProvider */
    public function testItWorks($expected, $actual)
    {
        self::assertTrue((new IsEqual($expected))->match($actual));
    }
}
