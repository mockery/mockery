<?php

namespace Mockery\Test\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfigurationBuilder;
use Mockery\Generator\StringManipulation\Pass\InstanceMockPass;

class InstanceMockPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldAppendConstructorAndPropertyForInstanceMock()
    {
        $builder = new MockConfigurationBuilder;
        $builder->setInstanceMock(true);
        $config = $builder->getMockConfiguration();
        $pass = new InstanceMockPass;
        $code = $pass->apply('class Dave { }', $config);
        $this->assertContains('public function __construct', $code);
        $this->assertContains('protected $_mockery_ignoreVerification', $code);
    }
}
