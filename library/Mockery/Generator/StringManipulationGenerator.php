<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use Mockery\Generator\StringManipulation\Pass\CallTypeHintPass;
use Mockery\Generator\StringManipulation\Pass\ClassNamePass;
use Mockery\Generator\StringManipulation\Pass\ClassPass;
use Mockery\Generator\StringManipulation\Pass\ConstantsPass;
use Mockery\Generator\StringManipulation\Pass\InstanceMockPass;
use Mockery\Generator\StringManipulation\Pass\InterfacePass;
use Mockery\Generator\StringManipulation\Pass\MagicMethodTypeHintsPass;
use Mockery\Generator\StringManipulation\Pass\MethodDefinitionPass;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Mockery\Generator\StringManipulation\Pass\RemoveBuiltinMethodsThatAreFinalPass;
use Mockery\Generator\StringManipulation\Pass\RemoveCommentsFromMockPass;
use Mockery\Generator\StringManipulation\Pass\RemoveDestructorPass;
use Mockery\Generator\StringManipulation\Pass\RemoveUnserializeForInternalSerializableClassesPass;
use Mockery\Generator\StringManipulation\Pass\TraitPass;
use Mockery\Generator\StringManipulation\Pass\AvoidMethodClashPass;
use Mockery\Generator\StringManipulation\Pass\ClassAttributesPass;

class StringManipulationGenerator implements Generator
{
    protected $passes = array();

    /**
     * Creates a new StringManipulationGenerator with the default passes
     *
     * @return StringManipulationGenerator
     */
    public static function withDefaultPasses()
    {
        return new static([
            new RemoveCommentsFromMockPass(),
            new CallTypeHintPass(),
            new MagicMethodTypeHintsPass(),
            new ClassPass(),
            new TraitPass(),
            new ClassNamePass(),
            new InstanceMockPass(),
            new InterfacePass(),
            new AvoidMethodClashPass(),
            new MethodDefinitionPass(),
            new RemoveUnserializeForInternalSerializableClassesPass(),
            new RemoveBuiltinMethodsThatAreFinalPass(),
            new RemoveDestructorPass(),
            new ConstantsPass(),
            new ClassAttributesPass(),
        ]);
    }

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
