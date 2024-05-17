<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Matcher;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @coversDefaultClass \Mockery
 */
final class IsEqualTest extends MockeryTestCase
{
    use MatcherDataProviderTrait;

    /**
     * @dataProvider isEqualDataProvider
     */
    public function testItWorks($expected, $actual): void
    {
        self::assertTrue(Mockery::isEqual($expected)->match($actual));
    }
}
