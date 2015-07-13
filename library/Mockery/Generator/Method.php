<?php

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
            return (string) $this->method->getReturnType();
        }
        return '';
    }
}
