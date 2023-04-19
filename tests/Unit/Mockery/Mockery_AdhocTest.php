<?php

namespace MockeryTest\Unit\Mockery;

use MockeryTest\Fixture\MockeryTest_NameOfExistingClassWithDestructor;

/**
 * Ad-hoc unit tests for various scenarios reported by users
 */
class Mockery_AdhocTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    protected $container;
    public function mockeryTestSetUp()
    {
        $this->container = new \Mockery\Container(\Mockery::getDefaultGenerator(), \Mockery::getDefaultLoader());
    }
    public function mockeryTestTearDown()
    {
        $this->container->mockery_close();
    }
    public function testSimplestMockCreation()
    {
        $m = $this->container->mock('MockeryTest_NameOfExistingClass');
        $this->assertInstanceOf(\MockeryTest_NameOfExistingClass::class, $m);
    }
    public function testMockeryInterfaceForClass()
    {
        $m = $this->container->mock('SplFileInfo');
        $this->assertInstanceOf(\Mockery\MockInterface::class, $m);
    }
    public function testMockeryInterfaceForNonExistingClass()
    {
        $m = $this->container->mock('ABC_IDontExist');
        $this->assertInstanceOf(\Mockery\MockInterface::class, $m);
    }
    public function testMockeryInterfaceForInterface()
    {
        $m = $this->container->mock('MockeryTest_NameOfInterface');
        $this->assertInstanceOf(\Mockery\MockInterface::class, $m);
    }
    public function testMockeryInterfaceForAbstract()
    {
        $m = $this->container->mock('MockeryTest_NameOfAbstract');
        $this->assertInstanceOf(\Mockery\MockInterface::class, $m);
    }
    public function testInvalidCountExceptionThrowsRuntimeExceptionOnIllegalComparativeSymbol()
    {
        $this->expectException('Mockery\Exception\RuntimeException');
        $e = new \Mockery\Exception\InvalidCountException();
        $e->setExpectedCountComparative('X');
    }
    public function testMockeryConstructAndDestructIsNotCalled()
    {
        MockeryTest_NameOfExistingClassWithDestructor::$isDestructorWasCalled = \false;
        // We pass no arguments in constructor, so it's not being called. Then destructor shouldn't be called too.
        $this->container->mock(MockeryTest_NameOfExistingClassWithDestructor::class);
        // Clear references to trigger destructor
        $this->container->mockery_close();
        $this->assertFalse(MockeryTest_NameOfExistingClassWithDestructor::$isDestructorWasCalled);
    }
    public function testMockeryConstructAndDestructIsCalled()
    {
        MockeryTest_NameOfExistingClassWithDestructor::$isDestructorWasCalled = \false;
        $this->container->mock(MockeryTest_NameOfExistingClassWithDestructor::class, []);
        // Clear references to trigger destructor
        $this->container->mockery_close();
        $this->assertTrue(MockeryTest_NameOfExistingClassWithDestructor::$isDestructorWasCalled);
    }
}
