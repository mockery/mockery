<?php

declare(strict_types=1);

namespace Tests\Unit\PHP74;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP74\Regression\Issue1402\Service;

/**
 * @coversDefaultClass Mockery
 * @requires PHP 7.4
 * @see https://github.com/mockery/mockery/issues/1402
 */
final class TestCase1402Test extends MockeryTestCase
{
    public function testMethod(): void
    {
        $banana = Mockery::mock(Service::class, [1])->makePartial();

        $banana->allows('test')
            ->andReturns(2);

        self::assertEquals(2, $banana->test());
    }
}
