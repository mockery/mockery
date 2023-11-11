<?php

namespace test\Mockery\Matcher;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Matcher\IsSame;

class IsSameTest extends MockeryTestCase
{
    use MatcherDataProviderTrait;

    /** @dataProvider isSameDataProvider */
    public function testItWorks($expected, $actual)
    {
        self::assertTrue(Mockery::isSame($expected)->match($actual));
    }
}
