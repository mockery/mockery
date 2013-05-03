<?php

namespace Mockery\Generator;

use  Mockery\Generator\StringManipulation\Pass\Pass;
use  Mockery\Generator\StringManipulation\Pass\ClassNamePass;
use  Mockery\Generator\StringManipulation\Pass\InterfacePass;
use  Mockery\Generator\StringManipulation\Pass\CallTypeHintPass;
use  Mockery\Generator\StringManipulation\Pass\InstanceMockPass;
use  Mockery\Generator\StringManipulation\Pass\MethodDefinitionPass;

class StringManipulationGenerator implements Generator
{
    protected $passes = array();

    public function __construct(array $passes)
    {
        $this->passes = $passes;
    }

    public function generate(MockConfiguration $config)
    {
        $code = file_get_contents(__DIR__ . '/../Mock.php');
        $className = $config->getShortName() ?: $config->generateName();

        foreach ($this->passes as $pass) {
            $code = $pass->apply($code, $config);
        }

        return new MockDefinition($config, $className, $code);
    }

    public function addPass(Pass $pass)
    {
        $this->passes[] = $pass;
    }
}
