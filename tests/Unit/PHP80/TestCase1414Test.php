<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP80;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 *
 * @requires PHP 8.0
 *
 * @see https://github.com/mockery/mockery/issues/1414
 */
final class TestCase1414Test extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testMockAnonymousClass(): void
    {
        $class = new class() extends stdClass {};

        $mock = Mockery::mock($class::class);

        self::assertInstanceOf($class::class, $mock);
    }
}
