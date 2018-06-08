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

namespace Mockery\Generator;

class Method
{
    private $method;

    public function __construct(\ReflectionMethod $method)
    {
        $this->method = $method;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->method, $method), $args);
    }

    public function getParameters()
    {
        return array_map(function ($parameter) {
            return new Parameter($parameter);
        }, $this->method->getParameters());
    }

    public function getReturnType()
    {
        if (version_compare(PHP_VERSION, '7.0.0-dev') >= 0 && $this->method->hasReturnType()) {
            $returnType = (string) $this->method->getReturnType();

            if ('self' === $returnType) {
                $returnType = "\\".$this->method->getDeclaringClass()->getName();
            } elseif (!\Mockery::isBuiltInType($returnType)) {
                $returnType = '\\'.$returnType;
            }

            if (version_compare(PHP_VERSION, '7.1.0-dev') >= 0 && $this->method->getReturnType()->allowsNull()) {
                $returnType = '?'.$returnType;
            }

            return $returnType;
        }
        return '';
    }
}
