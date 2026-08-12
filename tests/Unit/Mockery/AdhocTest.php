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

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Container;
use Mockery\Exception\InvalidCountException;
use Mockery\Exception\RuntimeException;
use Mockery\MockInterface;
use Override;
use PHP73\NameOfAbstract;
use PHP73\NameOfExistingClass;
use PHP73\NameOfExistingClassWithDestructor;
use PHP73\NameOfInterface;
use SplFileInfo;
use Throwable;

/**
 * Ad-hoc unit tests for various scenarios reported by users
 *
 * @coversDefaultClass \Mockery
 */
final class AdhocTest extends MockeryTestCase
{
    protected $container;

    #[Override]
    protected function mockeryTestSetUp(): void
    {
        $this->container = new Container(Mockery::getDefaultGenerator(), Mockery::getDefaultLoader());
    }

    #[Override]
    public function mockeryTestTearDown(): void
    {
        $this->container->mockery_close();
    }

    /**
     * @throws Throwable
     */
    public function testInvalidCountExceptionThrowsRuntimeExceptionOnIllegalComparativeSymbol(): void
    {
        $this->expectException(RuntimeException::class);
        $invalidCountException = new InvalidCountException();
        $invalidCountException->setExpectedCountComparative('X');
    }

    /**
     * @throws Throwable
     */
    public function testMockeryConstructAndDestructIsCalled(): void
    {
        NameOfExistingClassWithDestructor::$isDestructorWasCalled = false;

        $this->container->mock(NameOfExistingClassWithDestructor::class, []);
        // Clear references to trigger destructor
        $this->container->mockery_close();
        self::assertTrue(NameOfExistingClassWithDestructor::$isDestructorWasCalled);
    }

    /**
     * @throws Throwable
     */
    public function testMockeryConstructAndDestructIsNotCalled(): void
    {
        NameOfExistingClassWithDestructor::$isDestructorWasCalled = false;
        // We pass no arguments in constructor, so it's not being called. Then destructor shouldn't be called too.
        $this->container->mock(NameOfExistingClassWithDestructor::class);
        // Clear references to trigger destructor
        $this->container->mockery_close();
        self::assertFalse(NameOfExistingClassWithDestructor::$isDestructorWasCalled);
    }

    /**
     * @throws Throwable
     */
    public function testMockeryInterfaceForAbstract(): void
    {
        $m = $this->container->mock(NameOfAbstract::class);
        self::assertInstanceOf(MockInterface::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testMockeryInterfaceForClass(): void
    {
        $m = $this->container->mock(SplFileInfo::class);
        self::assertInstanceOf(MockInterface::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testMockeryInterfaceForInterface(): void
    {
        $m = $this->container->mock(NameOfInterface::class);
        self::assertInstanceOf(MockInterface::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testMockeryInterfaceForNonExistingClass(): void
    {
        $m = $this->container->mock('ABC_IDontExist');
        self::assertInstanceOf(MockInterface::class, $m);
    }

    /**
     * @throws Throwable
     */
    public function testSimplestMockCreation(): void
    {
        $m = $this->container->mock(NameOfExistingClass::class);
        self::assertInstanceOf(NameOfExistingClass::class, $m);
    }
}
