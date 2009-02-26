<?php

// Test helper
require_once dirname(__FILE__) . '/TestHelper.php';
require_once dirname(dirname(__FILE__)) . '/library/MockMe/Framework.php';
require_once dirname(__FILE__) . '/_files/Album.php';

class MockmeTest extends PHPUnit_Framework_TestCase
{

    public function testShouldCreateMockInheritingClassTypeFromOriginal()
    {
        $mock = mockme('MockMeTest_Album');
        $this->assertTrue($mock instanceof MockMeTest_Album);
    }

}
