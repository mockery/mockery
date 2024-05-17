<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery\Matcher;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @coversDefaultClass \Mockery
 */
final class IsSameTest extends MockeryTestCase
{
    use MatcherDataProviderTrait;

    /**
     * @dataProvider isSameDataProvider
     */
    public function testItWorks($expected, $actual): void
    {
        self::assertTrue(Mockery::isSame($expected)->match($actual));
    }
}
