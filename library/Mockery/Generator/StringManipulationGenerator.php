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
        $className = $config->getName() ?: $config->generateName();

        $namedConfig = $config->rename($className);

        foreach ($this->passes as $pass) {
            $code = $pass->apply($code, $namedConfig);
        }

        return new MockDefinition($namedConfig, $code);
    }

    public function addPass(Pass $pass)
    {
        $this->passes[] = $pass;
    }
}
