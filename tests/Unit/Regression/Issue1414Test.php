<?php

declare(strict_types=1);

namespace Mockery\Tests\Unit\Regression;

use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mockery
 * @see https://github.com/mockery/mockery/issues/1414
 * @requires PHP 8.0
 */
final class Issue1414Test extends TestCase
{
    public function testMockAnonymousClass(): void
    {
        $class = new class() extends \stdClass {};

        $mock = Mockery::mock($class::class);

        self::assertInstanceOf($class::class, $mock);
    }
}
