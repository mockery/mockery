<?php

declare(strict_types=1);

namespace Tests\Unit\Mockery;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Container;
use Mockery\Exception\RuntimeException;
use Mockery\MockInterface;
use PHP73\NameOfAbstract;
use PHP73\NameOfExistingClass;
use PHP73\NameOfExistingClassWithDestructor;
use PHP73\NameOfInterface;
use SplFileInfo;

/**
 * Ad-hoc unit tests for various scenarios reported by users
 * @coversDefaultClass \Mockery
 */
final class AdhocTest extends MockeryTestCase
{
    protected $container;

    protected function mockeryTestSetUp(): void
    {
        $this->container = new Container(Mockery::getDefaultGenerator(), Mockery::getDefaultLoader());
    }

    public function mockeryTestTearDown(): void
    {
        $this->container->mockery_close();
    }

    public function testInvalidCountExceptionThrowsRuntimeExceptionOnIllegalComparativeSymbol(): void
    {
        $this->expectException(RuntimeException::class);
        $e = new \Mockery\Exception\InvalidCountException();
        $e->setExpectedCountComparative('X');
    }

    public function testMockeryConstructAndDestructIsCalled(): void
    {
        NameOfExistingClassWithDestructor::$isDestructorWasCalled = false;

        $this->container->mock(NameOfExistingClassWithDestructor::class, []);
        // Clear references to trigger destructor
        $this->container->mockery_close();
        self::assertTrue(NameOfExistingClassWithDestructor::$isDestructorWasCalled);
    }

    public function testMockeryConstructAndDestructIsNotCalled(): void
    {
        NameOfExistingClassWithDestructor::$isDestructorWasCalled = false;
        // We pass no arguments in constructor, so it's not being called. Then destructor shouldn't be called too.
        $this->container->mock(NameOfExistingClassWithDestructor::class);
        // Clear references to trigger destructor
        $this->container->mockery_close();
        self::assertFalse(NameOfExistingClassWithDestructor::$isDestructorWasCalled);
    }

    public function testMockeryInterfaceForAbstract(): void
    {
        $m = $this->container->mock(NameOfAbstract::class);
        self::assertInstanceOf(MockInterface::class, $m);
    }

    public function testMockeryInterfaceForClass(): void
    {
        $m = $this->container->mock(SplFileInfo::class);
        self::assertInstanceOf(MockInterface::class, $m);
    }

    public function testMockeryInterfaceForInterface(): void
    {
        $m = $this->container->mock(NameOfInterface::class);
        self::assertInstanceOf(MockInterface::class, $m);
    }

    public function testMockeryInterfaceForNonExistingClass(): void
    {
        $m = $this->container->mock('ABC_IDontExist');
        self::assertInstanceOf(MockInterface::class, $m);
    }

    public function testSimplestMockCreation(): void
    {
        $m = $this->container->mock(NameOfExistingClass::class);
        self::assertInstanceOf(NameOfExistingClass::class, $m);
    }
}
