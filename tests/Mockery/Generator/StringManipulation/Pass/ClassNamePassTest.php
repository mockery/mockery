<?php

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery as m;
use Mockery\Generator\StringManipulation\Pass\ClassNamePass;
use Mockery\Generator\MockConfiguration;

class ClassNamePassTest extends \PHPUnit_Framework_TestCase
{
    const CODE = "namespace Mockery; class Mock {}";

    public function setup()
    {
       $this->pass = new ClassNamePass();
    }

    /**
     * @test
     */
    public function shouldRemoveNamespaceDefinition()
    {
        $config = new MockConfiguration(array(), array(), array(), "Dave\Dave");
        $code = $this->pass->apply(static::CODE, $config);
        $this->assertNotContains('namespace Mockery;', $code);
    }

    /**
     * @test
     */
    public function shouldReplaceNamespaceIfClassNameIsNamespaced()
    {
        $config = new MockConfiguration(array(), array(), array(), "Dave\Dave");
        $code = $this->pass->apply(static::CODE, $config);
        $this->assertNotContains('namespace Mockery;', $code);
        $this->assertContains('namespace Dave;', $code);
    }

    /**
     * @test
     */
    public function shouldReplaceClassNameWithSpecifiedName()
    {
        $config = new MockConfiguration(array(), array(), array(), "Dave");
        $code = $this->pass->apply(static::CODE, $config);
        $this->assertContains('class Dave', $code);
    }
}
