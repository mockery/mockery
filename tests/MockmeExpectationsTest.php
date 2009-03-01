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
            $this->assertTrue(mockme_verify());
        } catch (Exception $e) {
            $this->fail('Mock object checking threw an Exception reading: ' . $e->getMessage());
        }
    }

    public function testShouldThrowDefaultExceptionIfMethodCallCountIsUnexpected()
    {
        $mock = mockme('MockMeTest_Album');
        $mock->shouldReceive('getName');
        //$mock->getName();
        //$mock->getName();
        //try {
        //    mockme_verify();
        //    $this->fail('Expected exception was not thrown');
        //} catch (MockMe_Exception $e) {
        //}
    }


}
