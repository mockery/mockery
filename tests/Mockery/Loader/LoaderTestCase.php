<?php

namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;
use Mockery\Generator\MockConfiguration;

abstract class LoaderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function loadLoadsTheCode()
    {
        $config = new MockConfiguration;
        $className = 'Mock_' . uniqid();
        $code = "<?php class $className { } ";

        $definition = new MockDefinition($config, $className, $code);

        $this->getLoader()->load($definition);

        $this->assertTrue(class_exists($className));
    }

    abstract function getLoader();
}
