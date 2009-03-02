<?php

// Test helper
require_once dirname(__FILE__) . '/TestHelper.php';
require_once dirname(dirname(__FILE__)) . '/library/MockMe/Framework.php';
require_once dirname(__FILE__) . '/_files/Album.php';

class RegressionTest extends PHPUnit_Framework_TestCase
{

    public function testShouldCreateMockInheritingClassTypeFromOriginal()
    {
        $mock = mockme('MockMeTest_EmptyClass');
        $this->assertTrue($mock instanceof MockMeTest_EmptyClass);
    }

    public function testShouldCreateMockUsingCustomNameIfSupplied()
    {
        $mock = mockme('MockMeTest_EmptyClass', 'MockMeTest_CustomNamed');
        $this->assertTrue($mock instanceof MockMeTest_CustomNamed);
    }

    public function testShouldReturnTrueOnValidationByDefaultIfNoExpectationsSet()
    {
        $mock = mockme('MockMeTest_EmptyClass');
        try {
            $this->assertTrue($mock->mockme_verify());
        } catch (Exception $e) {
            $this->fail('Mock object checking threw an Exception reading: ' . $e->getMessage());
        }
    }


}
