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

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Override;
use PHP73\SubclassWithFinalWakeup;
use PHP73\TestWithFinalWakeup;
use PHP73\TestWithNonFinalWakeup;
use Throwable;

/**
 * @coversDefaultClass \Mockery
 */
final class MockClassWithFinalWakeupTest extends MockeryTestCase
{
    protected $container;

    #[Override]
    protected function mockeryTestSetUp(): void
    {
        $this->container = new \Mockery\Container();
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
    public function testCreateMockForClassWithFinalWakeup(): void
    {
        $mock = $this->container->mock(TestWithFinalWakeup::class);
        self::assertInstanceOf(TestWithFinalWakeup::class, $mock);
        self::assertSame(TestWithFinalWakeup::class . '::__wakeup', $mock->__wakeup());

        $mock = $this->container->mock(SubclassWithFinalWakeup::class);
        self::assertInstanceOf(SubclassWithFinalWakeup::class, $mock);
        self::assertSame(TestWithFinalWakeup::class . '::__wakeup', $mock->__wakeup());
    }

    /**
     * @throws Throwable
     */
    public function testCreateMockForClassWithNonFinalWakeup(): void
    {
        $mock = $this->container->mock(TestWithNonFinalWakeup::class);
        self::assertInstanceOf(TestWithNonFinalWakeup::class, $mock);

        // Make sure __wakeup is overridden and doesn't return anything.
        self::assertNull($mock->__wakeup());
    }
}
