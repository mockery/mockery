<?php

declare(strict_types=1);

/**
 * Mockery (https://docs.mockery.io/en/stable/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @see       https://github.com/mockery/mockery for the canonical source repository
 */

namespace Tests\Unit\PHP73;

use PHP73\MethodWithVoidReturnType;
use Tests\Unit\AbstractTestCase;
use Throwable;

use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingVoidMethodsTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    public function testItCanStubAndMockVoidMethods(): void
    {
        $mock = mock(MethodWithVoidReturnType::class);

        $mock->expects('foo');

        $mock->foo();
    }

    /**
     * @throws Throwable
     */
    public function testItShouldSuccessfullyBuildTheMock(): void
    {
        self::assertInstanceOf(MethodWithVoidReturnType::class, mock(MethodWithVoidReturnType::class));
    }
}
