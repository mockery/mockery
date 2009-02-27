<?php

// Test helper
require_once dirname(__FILE__) . '/TestHelper.php';
require_once dirname(dirname(__FILE__)) . '/library/MockMe/Framework.php';
require_once dirname(__FILE__) . '/_files/Album.php';

class MockmeTest extends PHPUnit_Framework_TestCase
{

    // Basic Mocking Of Existing Classes

    public function testShouldCreateMockInheritingClassTypeFromOriginal()
    {
        $mock = mockme('MockMeTest_EmptyClass');
        $this->assertTrue($mock instanceof MockMeTest_EmptyClass);
    }

    public function testShouldCreateMockInheritingTypeIfOriginalAnInterface()
    {
        $mock = mockme('MockMeTest_Interface');
        $this->assertTrue($mock instanceof MockMeTest_Interface);
    }

    public function testShouldCreateMockWhereAnyInterfaceMethodsAreImplemented() 
    {
        $mock = mockme('MockMeTest_InterfaceWithAbstractMethod');
        $this->assertTrue($mock instanceof MockMeTest_InterfaceWithAbstractMethod);
    }

    public function testShouldCreateMockWhereAnyAbstractMethodsAreImplemented() 
    {
        $mock = mockme('MockMeTest_AbstractWithAbstractMethod');
        $this->assertTrue($mock instanceof MockMeTest_AbstractWithAbstractMethod);
    }

    public function testShouldImplementAbstractMethodsWithFullParameterList() 
    {
        $mock = mockme('MockMeTest_InterfaceWithAbstractMethodAndParameters');
        $this->assertTrue($mock instanceof MockMeTest_InterfaceWithAbstractMethodAndParameters);
    }

    public function testShouldCreateMockUsingCustomNameIfSupplied() 
    {
        $mock = mockme('MockMeTest_EmptyClass', 'MockMeTest_CustomNamed');
        $this->assertTrue($mock instanceof MockMeTest_CustomNamed);
    }

    // Stubbing Of Non-existing Classes

    public function testShouldCreateNewClassWithGivenNameIfNotYetExisting() 
    {
        $mock = mockme('MockMeTest_DoesntExist');
        $this->assertTrue($mock instanceof MockMeTest_DoesntExist);
    }

    public function testShouldCreateStubFromAnArrayOfMethodsAndReturnValues()
    {
        $mock = mockme('MockMeTest_DoesntExist2', array('get' => 'foo'));
        $this->assertEquals('foo', $mock->get());
    }


}
