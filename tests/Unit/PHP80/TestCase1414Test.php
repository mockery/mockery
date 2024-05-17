<?php

declare(strict_types=1);

namespace Tests\Unit\PHP80;

use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @coversDefaultClass Mockery
 * @requires PHP 8.0
 * @see https://github.com/mockery/mockery/issues/1414
 */
final class TestCase1414Test extends TestCase
{
    public function testMockAnonymousClass(): void
    {
        $class = new class () extends stdClass {};

        $mock = Mockery::mock($class::class);

        self::assertInstanceOf($class::class, $mock);
    }
}
