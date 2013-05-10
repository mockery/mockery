<?php

namespace Mockery\Test\Generator\StringManipulation\Pass;

use Mockery as m;
use Mockery\Generator\StringManipulation\Pass\InstanceMockPass;
use Mockery\Generator\MockConfiguration;

class InstanceMockPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldAppendConstructorAndPropertyForInstanceMock()
    {
        $config = new MockConfiguration;
        $config->setInstanceMock(true);
        $pass = new InstanceMockPass;
        $code = $pass->apply('class Dave { }', $config);
        $this->assertContains('public function __construct', $code);
        $this->assertContains('protected $_mockery_ignoreVerification', $code);
    }
}
