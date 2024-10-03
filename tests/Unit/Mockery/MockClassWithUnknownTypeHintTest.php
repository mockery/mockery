<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use PHP73\HasUnknownClassAsTypeHintOnMethod;

/**
 * @coversDefaultClass \Mockery
 */
final class MockClassWithUnknownTypeHintTest extends MockeryTestCase
{
    public function testItShouldSuccessfullyBuildTheMock(): void
    {
        $mock = \mock(HasUnknownClassAsTypeHintOnMethod::class);

        self::assertInstanceOf(MockInterface::class, $mock);
    }
}
