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
use Mockery\Container;
use Override;
use PHP73\SubclassWithFinalToString;
use PHP73\TestWithFinalToString;
use PHP73\TestWithNonFinalToString;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 */
final class MockClassWithFinalToStringTest extends MockeryTestCase
{
    protected $container;

    #[Override]
    protected function mockeryTestSetUp(): void
    {
        $this->container = new Container();
    }

    #[Override]
    protected function mockeryTestTearDown(): void
    {
        $this->container->mockery_close();
    }

    /**
     * Test that we are able to create partial mocks of classes that have
     * a __wakeup method marked as final. As long as __wakeup is not one of the
     * mocked methods.
     *
     * @throws Throwable
     */
    public function testCreateMockForClassWithFinalToString(): void
    {
        $mock = $this->container->mock(TestWithFinalToString::class);
        self::assertInstanceOf(TestWithFinalToString::class, $mock);
        self::assertSame(TestWithFinalToString::class . '::__toString', $mock->__toString());

        $mock = $this->container->mock(SubclassWithFinalToString::class);
        self::assertInstanceOf(TestWithFinalToString::class, $mock);
        self::assertSame(TestWithFinalToString::class . '::__toString', $mock->__toString());
    }

    /**
     * @throws Throwable
     */
    public function testCreateMockForClassWithNonFinalToString(): void
    {
        $mock = $this->container->mock(TestWithNonFinalToString::class);
        self::assertInstanceOf(TestWithNonFinalToString::class, $mock);

        // Make sure __toString is overridden.
        self::assertNotSame('bar', $mock->__toString());
    }
}
