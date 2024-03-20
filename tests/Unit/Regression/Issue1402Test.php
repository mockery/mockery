<?php

declare(strict_types=1);

namespace Mockery\Tests\Unit\Regression;

use Fixture\PHP74\Regression\Issue1402\Service;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @requires PHP 7.4
 */
final class Issue1402Test extends MockeryTestCase
{
    public function testMethod(): void {
        $banana = Mockery::mock(Service::class, [1])
                         ->makePartial();

        $banana->allows('test')->andReturns(2);

        self::assertEquals(2, $banana->test());
    }
}
