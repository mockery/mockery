<?php

declare(strict_types=1);

namespace Tests\Unit\PHP73;

use PHP73\MethodWithVoidReturnType;
use Tests\Unit\AbstractTestCase;

/**
 * @coversDefaultClass \Mockery
 */
final class MockingVoidMethodsTest extends AbstractTestCase
{
    public function testItCanStubAndMockVoidMethods(): void
    {
        $mock = \mock(MethodWithVoidReturnType::class);

        $mock->expects('foo');

        $mock->foo();
    }

    public function testItShouldSuccessfullyBuildTheMock(): void
    {
        self::assertInstanceOf(MethodWithVoidReturnType::class, \mock(MethodWithVoidReturnType::class));
    }
}
