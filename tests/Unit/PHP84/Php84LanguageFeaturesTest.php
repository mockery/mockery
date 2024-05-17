<?php

declare(strict_types=1);

namespace Tests\Unit\PHP84;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;

/**
 * @requires PHP 8.4.0-dev
 * @coversDefaultClass \Mockery
 */
final class Php84LanguageFeaturesTest extends MockeryTestCase
{
    public function testExample(): void
    {
        self::assertInstanceOf(stdClass::class, Mockery::mock(stdClass::class));
    }
}
