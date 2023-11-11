<?php

namespace test\Mockery\Matcher;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Matcher\IsEqual;

class IsEqualTest extends MockeryTestCase
{
    use MatcherDataProviderTrait;

    /** @dataProvider isEqualDataProvider */
    public function testItWorks($expected, $actual)
    {
        self::assertTrue(Mockery::isEqual($expected)->match($actual));
    }
}
