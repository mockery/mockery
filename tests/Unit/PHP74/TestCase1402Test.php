<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP74;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP74\Regression\Issue1402\Service;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 *
 * @requires PHP 7.4
 *
 * @see https://github.com/mockery/mockery/issues/1402
 */
final class TestCase1402Test extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testMethod(): void
    {
        $banana = Mockery::mock(Service::class, [1])->makePartial();

        $banana->allows('test')
            ->andReturns(2);

        self::assertSame(2, $banana->test());
    }
}
