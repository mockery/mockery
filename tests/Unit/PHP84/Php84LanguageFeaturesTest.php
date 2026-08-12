<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP84;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;
use Throwable;

/**
 * @requires PHP 8.4.0-dev
 *
 * @coversDefaultClass \Mockery
 */
final class Php84LanguageFeaturesTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testExample(): void
    {
        self::assertInstanceOf(stdClass::class, Mockery::mock(stdClass::class));
    }
}
