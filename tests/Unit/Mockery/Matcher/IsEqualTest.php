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

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 */
final class IsEqualTest extends MockeryTestCase
{
    use MatcherDataProviderTrait;

    /**
     * @dataProvider isEqualDataProvider
     *
     * @throws Throwable
     */
    #[DataProvider('isEqualDataProvider')]
    public function testItWorks($expected, $actual): void
    {
        self::assertTrue(Mockery::isEqual($expected)->match($actual));
    }
}
