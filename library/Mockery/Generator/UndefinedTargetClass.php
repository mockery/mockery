<?php

namespace Mockery\Generator;

class UndefinedTargetClass 
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isAbstract()
    {
        return false;
    }

    public function isFinal()
    {
        return false;
    }

    public function getMethods()
    {
        return array();
    }
}
