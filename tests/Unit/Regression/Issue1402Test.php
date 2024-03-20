<?php

declare(strict_types=1);

namespace Mockery\Tests\Unit\Regression;

use Fixture\Regression\Issue1402\Service;
use Fixture\Regression\Issue1402\InitTrait;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class Issue1402Test extends MockeryTestCase
{
    public function testMethod(): void {
        $banana = Mockery::mock(Service::class, [1])
                         ->makePartial();

        $banana->allows('test')->andReturns(2);

        self::assertEquals(2, $banana->test());
    }
}

