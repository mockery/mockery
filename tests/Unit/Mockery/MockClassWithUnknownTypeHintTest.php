<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\Mockery;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use PHP73\HasUnknownClassAsTypeHintOnMethod;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockClassWithUnknownTypeHintTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testItShouldSuccessfullyBuildTheMock(): void
    {
        $mock = mock(HasUnknownClassAsTypeHintOnMethod::class);

        self::assertInstanceOf(MockInterface::class, $mock);
    }
}
