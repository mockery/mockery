<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license   https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link      https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use Mockery\Reflector;

class Method
{
    /** @var \ReflectionMethod */
    private $method;

    public function __construct(\ReflectionMethod $method)
    {
        $this->method = $method;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->method, $method), $args);
    }

    /**
     * @return Parameter[]
     */
    public function getParameters()
    {
        return array_map(function (\ReflectionParameter $parameter) {
            return new Parameter($parameter);
        }, $this->method->getParameters());
    }

    /**
     * @return string|null
     */
    public function getReturnType()
    {
        return Reflector::getReturnType($this->method);
    }
}
