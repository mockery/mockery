<?php

// Test helper
require_once dirname(__FILE__) . '/TestHelper.php';
require_once dirname(dirname(__FILE__)) . '/library/Mockery/Framework.php';
require_once dirname(__FILE__) . '/_files/Album.php';

class MockeryTest extends PHPUnit_Framework_TestCase
{

    // Basic Mocking Of Existing Classes

    public function testShouldCreateMockInheritingClassTypeFromOriginal()
    {
        $mock = mockery('MockeryTest_EmptyClass');
        $this->assertTrue($mock instanceof MockeryTest_EmptyClass);
    }

    public function testShouldCreateMockInheritingTypeIfOriginalAnInterface()
    {
        $mock = mockery('MockeryTest_Interface');
        $this->assertTrue($mock instanceof MockeryTest_Interface);
    }

    public function testShouldCreateMockWhereAnyInterfaceMethodsAreImplemented()
    {
        $mock = mockery('MockeryTest_InterfaceWithAbstractMethod');
        $this->assertTrue($mock instanceof MockeryTest_InterfaceWithAbstractMethod);
    }

    public function testShouldCreateMockWhereAnyAbstractMethodsAreImplemented()
    {
        $mock = mockery('MockeryTest_AbstractWithAbstractMethod');
        $this->assertTrue($mock instanceof MockeryTest_AbstractWithAbstractMethod);
    }

    public function testShouldImplementAbstractMethodsWithFullParameterList()
    {
        $mock = mockery('MockeryTest_InterfaceWithAbstractMethodAndParameters');
        $this->assertTrue($mock instanceof MockeryTest_InterfaceWithAbstractMethodAndParameters);
    }

    public function testShouldCreateMockUsingCustomNameIfSupplied()
    {
        $mock = mockery('MockeryTest_EmptyClass', 'MockeryTest_CustomNamed');
        $this->assertTrue($mock instanceof MockeryTest_CustomNamed);
    }

    // Stubbing of Existing Classes

    public function testShouldCreateStubInheritingClassTypeFromOriginal()
    {
        $mock = mockery('MockeryTest_SimpleClass', array('get' => 'foo'));
        $this->assertEquals('foo', $mock->get());
    }

    // Stubbing Of Non-existing Classes

    public function testShouldCreateNewClassWithGivenNameIfNotYetExisting()
    {
        $mock = mockery('MockeryTest_DoesntExist');
        $this->assertTrue($mock instanceof MockeryTest_DoesntExist);
    }

    public function testShouldWithoutMethodHashCreateAMockObject()
    {
        $mock = mockery('MockeryTest_DoesntExist3');
        $this->assertTrue($mock->shouldReceive('doSomething') instanceof Mockery_Expectation);
    }

    public function testShouldCreateStubFromAnArrayOfMethodsAndReturnValues()
    {
        $mock = mockery('MockeryTest_DoesntExist2', array('get' => 'foo'));
        $this->assertEquals('foo', $mock->get());
    }

    // Untouched classes

    public function testShouldLeaveMethodsUntouchedUnlessExpectationsWereCreated()
    {
        mockery('MockeryTest_SimpleClass');
        $nonMock = new MockeryTest_SimpleClass;
        $this->assertEquals('simple', $nonMock->get());
    }

    // Touched classes

    public function testShouldNotLeaveMethodsUntouchedIfExpectationsWereCreated()
    {
        $mock = mockery('MockeryTest_SimpleClass');
        $mock->shouldReceive('set')->once();
        $mock->get();
        $mock->set();
        try {
            $mock->mockery_verify();
            $this->fail();
        } catch(Mockery_Exception $e) {
        }
    }

}
