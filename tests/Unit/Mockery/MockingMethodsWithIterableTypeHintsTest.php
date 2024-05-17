<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHP73\MethodWithIterableTypeHints;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingMethodsWithIterableTypeHintsTest extends MockeryTestCase
{
    public function testItShouldSuccessfullyBuildTheMock(): void
    {
        $mock = mock(MethodWithIterableTypeHints::class);

        self::assertInstanceOf(MethodWithIterableTypeHints::class, $mock);
    }
}
