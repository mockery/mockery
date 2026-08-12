<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP85;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Throwable;

use function mock;

/**
 * @requires PHP 8.5.0-dev
 *
 * @coversDefaultClass \Mockery
 */
final class Php85LanguageFeaturesTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testExample(): void
    {
        $mock = mock();
        self::assertSame($mock, $mock->expects('zero')->with(0)->zeroOrMoreTimes()->getMock());

        $mock->expects('false')->andReturnFalse()->once();
        self::assertFalse($mock->false());

        $mock->expects('foo')->andReturn('foo')->once();
        self::assertSame('foo', $mock->foo());

        $mock->expects('nullable')->andReturnNull()->once();
        self::assertNull($mock->nullable());

        $mock->expects('self')->andReturnSelf()->once();
        self::assertSame($mock, $mock->self());

        $mock->expects('true')->andReturnTrue()->once();
        self::assertTrue($mock->true());
    }
}
