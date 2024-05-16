<?php

declare(strict_types=1);

namespace Tests\Unit\PHP81;

use Fixture\PHP81\HandlerClass;
use Fixture\PHP81\MockClass;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @requires PHP 8.1.0-dev
 * @coversDefaultClass \Mockery
 */
final class Php81LanguageFeaturesTest extends MockeryTestCase
{
    public function testNewInitializerExpression()
    {
        $class = mock(MockClass::class)
            ->expects('test')
            ->with('test')
            ->andReturn('it works')
            ->getMock();

        self::assertSame('it works', (new HandlerClass())->doStuff($class));
    }
}
