<?php
/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @copyright  Copyright (c) 2010 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use Mockery\Generator\DefinedTargetClass;
use Mockery\Generator\Method;

class MagicMethodTypeHintsPass implements Pass
{
    /**
     * @var array $mockMagicMethods
     */
    private $mockMagicMethods = array(
        '__construct',
        '__destruct',
        '__call',
        '__callStatic',
        '__get',
        '__set',
        '__isset',
        '__unset',
        '__sleep',
        '__wakeup',
        '__tostring',
        '__invoke',
        '__set_state',
        '__clone',
        '__debugInfo'
    );

    /**
     * Apply implementation.
     *
     * @param $code
     * @param MockConfiguration $config
     * @return string
     */
    public function apply($code, MockConfiguration $config)
    {
        $magicMethods = $this->getMagicMethods($config->getTargetClass());

        foreach ($magicMethods as $method) {
            $code = $this->applyMagicTypeHints($method, $code);
        }

        return $code;
    }

    /**
     * Returns the magic methods within the
     * passed DefinedTargetClass.
     *
     * @param DefinedTargetClass $class
     * @return array
     */
    public function getMagicMethods(DefinedTargetClass $class)
    {
        return array_filter($class->getMethods(), function(Method $method) {
            return in_array($method->getName(), $this->mockMagicMethods);
        });
    }

    /**
     * Applies type hints of magic methods from
     * class to the passed code.
     *
     * @param Method $method
     * @param $code
     * @return string
     */
    private function applyMagicTypeHints(Method $method, $code)
    {
        $methodName = strtolower($method->getName());

        if ($methodName == '__isset') {
            $code = str_replace(
                'public function __isset($name)',
                $this->getMethodDeclaration($method),
                $code
            );
        }

        if ($methodName == '__tostring') {
            $code = str_replace(
                'public function __toString()',
                $this->getMethodDeclaration($method),
                $code
            );
        }

        if ($methodName == '__call') {
            $code = str_replace(
                'public function __call($method, array $args)',
                $this->getMethodDeclaration($method),
                $code
            );
        }

        if ($methodName == '__callStatic') {
            $code = str_replace(
                'public function __callStatic($method, array $args)',
                $this->getMethodDeclaration($method),
                $code
            );
        }

        return $code;
    }

    /**
     * Gets the declaration code for the passed method.
     *
     * @param Method $method
     * @return string
     */
    private function getMethodDeclaration(Method $method)
    {
        $declaration = 'public function '.$method->getName().'(';

        foreach ($method->getParameters() as $parameter) {
            $declaration .= $parameter->getTypeHintAsString().' ';
            $declaration .= '$'.$parameter->getName();
            $declaration .= ',';
        }
        $declaration = rtrim($declaration, ',');
        $declaration .= ')';
        $declaration .= ' : '.$method->getReturnType();

        return $declaration;
    }


}
