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
use Mockery\Exception\BadMethodCallException;
use PHP73\TestWithMethodOverloading;
use PHP73\TestWithMethodOverloadingWithoutCall;
use Throwable;

use function get_class;
use function mock;

/**
 * @coversDefaultClass \Mockery
 */
final class MockClassWithMethodOverloadingTest extends MockeryTestCase
{
    /**
     * @throws Throwable
     */
    public function testCreateMockForClassWithMethodOverloading(): void
    {
        $mock = mock(TestWithMethodOverloading::class)->makePartial();

        self::assertInstanceOf(TestWithMethodOverloading::class, $mock);

        self::assertSame(42, $mock->theAnswer());
    }

    /**
     * @throws Throwable
     */
    public function testCreateMockForClassWithMethodOverloadingWithExistingMethod(): void
    {
        $mock = mock(TestWithMethodOverloading::class)->makePartial();

        self::assertInstanceOf(TestWithMethodOverloading::class, $mock);

        self::assertSame(1, $mock->thisIsRealMethod());
    }

    /**
     * @throws Throwable
     */
    public function testThrowsWhenMethodDoesNotExist(): void
    {
        $mock = mock(TestWithMethodOverloadingWithoutCall::class)->makePartial();

        self::assertInstanceOf(TestWithMethodOverloadingWithoutCall::class, $mock);

        try {
            $mock->randomMethod();
        } catch (BadMethodCallException $e) {
            self::assertStringContainsString(
                'Method ' . get_class($mock) . '::randomMethod() does not exist on this mock object',
                $e->getMessage()
            );
            $e->dismiss();
        }
    }
}
