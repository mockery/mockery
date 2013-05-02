<?php

namespace Mockery\Generator;

class Parameter 
{
    private $rfp;

    public function __construct(\ReflectionParameter $rfp)
    {
        $this->rfp = $rfp;
    }

    public function __call($method, array $args)
    {
        return call_user_func_array(array($this->rfp, $method), $args);
    }
}
