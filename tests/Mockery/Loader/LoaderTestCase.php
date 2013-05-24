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
        $className = 'Mock_' . uniqid();
        $config = new MockConfiguration(array(), array(), array(), $className);
        $code = "<?php class $className { } ";

        $definition = new MockDefinition($config, $code);

        $this->getLoader()->load($definition);

        $this->assertTrue(class_exists($className));
    }

    abstract function getLoader();
}
