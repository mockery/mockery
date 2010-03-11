<?php

// Test helper
require_once dirname(__FILE__) . '/TestHelper.php';
require_once dirname(dirname(__FILE__)) . '/library/Mockery/Framework.php';
require_once dirname(__FILE__) . '/_files/Album.php';

class RegressionTest extends PHPUnit_Framework_TestCase
{

    public function testShouldCreateMockInheritingClassTypeFromOriginal()
    {
        $mock = mockery('MockeryTest_EmptyClass');
        $this->assertTrue($mock instanceof MockeryTest_EmptyClass);
    }

    public function testShouldCreateMockUsingCustomNameIfSupplied()
    {
        $mock = mockery('MockeryTest_EmptyClass', 'MockeryTest_CustomNamed');
        $this->assertTrue($mock instanceof MockeryTest_CustomNamed);
    }

    public function testShouldReturnTrueOnValidationByDefaultIfNoExpectationsSet()
    {
        $mock = mockery('MockeryTest_EmptyClass');
        try {
            $this->assertTrue($mock->mockery_verify());
        } catch (Exception $e) {
            $this->fail('Mock object checking threw an Exception reading: ' . $e->getMessage());
        }
    }
}
