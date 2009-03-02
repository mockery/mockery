<?php

// Test helper
require_once dirname(__FILE__) . '/TestHelper.php';
require_once dirname(dirname(__FILE__)) . '/library/MockMe/Framework.php';
require_once dirname(__FILE__) . '/_files/Album.php';

class MockmeExpectationsTest extends PHPUnit_Framework_TestCase
{

    public function testShouldReturnTrueOnValidationByDefaultIfNoExpectationsSet()
    {
        $mock = mockme('MockMeTest_EmptyClass');
        try {
            $this->assertTrue($mock->mockme_verify());
        } catch (Exception $e) {
            $this->fail('Mock object checking threw an Exception reading: ' . $e->getMessage());
        }
    }

    public function testShouldThrowDefaultExceptionIfMethodCallCountIsUnexpected()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName');
        $mock->getName();
        $mock->getName();
        try {
            $mock->mockme_verify();
            $this->fail('Expected exception was not thrown');
        } catch (MockMe_Exception $e) {
        }
    }

    public function testShouldExpectNumberOfMethodsInTimesTerm()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->times(5);
        for ($i=0; $i <= 4; $i++) {
            $mock->getName();
        }
        $this->assertTrue($mock->mockme_verify());
    }

    public function testShouldReturnSelfFromTimesTermInvocation()
    {
        $mock = mockme('MockMeTest_Album');
    	$object = $mock->shouldReceive('getName')->times(1);
    	$this->assertTrue($object instanceof MockMe_Expectation);
    }

    public function testShouldSetReturnValueInTermsAndDefaultToReturningValueForAllCalls()
    {
        $mock = mockme('MockMeTest_Album');
    	$mock->shouldReceive('getName')->andReturn('Joe');
    	$mock->getName();
    	$this->assertEquals('Joe', $mock->getName());
    }

    public function testShouldSetReturnValueButThrowDefaultExceptionIfMethodCallCountIsUnexpected()
    {
        $mock = mockme('MockMeTest_Album');
    	$mock->shouldReceive('getName')->andReturn('Joe');
    	$mock->getName();
    	$mock->getName();
    	try {
            $mock->mockme_verify();
            $this->fail('Expected exception was not thrown');
        } catch (MockMe_Exception $e) {
        }
    }

    public function testShouldReturnValuesInOrderOfSettingButReturnLastValueRemainingOnOtherCalls()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName')->andReturn('Joe', 'Paddy', 'Travis');
        for ($i=0; $i <= 5; $i++) {
            $mock->getName();
        }
        $this->assertEquals('Travis', $mock->getName());
    }

    public function testShouldReturnSelfFromReturnTermInvocation()
    {
        $mock = mockme('MockMeTest_Album');
        $object = $mock->shouldReceive('getName')->times(1)->andReturn('Joe');
        $this->assertTrue($object instanceof MockMe_Expectation);
    }


}
