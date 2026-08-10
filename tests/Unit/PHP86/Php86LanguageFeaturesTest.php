<?php

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

declare(strict_types=1);

namespace Tests\Unit\PHP86;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;

/**
 * @requires PHP 8.6.0-dev
 * @coversDefaultClass \Mockery
 */
final class Php86LanguageFeaturesTest extends MockeryTestCase
{
    public function testExample(): void
    {
        self::assertInstanceOf(stdClass::class, Mockery::mock(stdClass::class));
    }
}
