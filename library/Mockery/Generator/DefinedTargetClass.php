<?php

namespace Mockery\Generator;

class DefinedTargetClass 
{
    private $rfc;

    public function __construct(\ReflectionClass $rfc)
    {
        $this->rfc = $rfc;
    }

    public static function factory($name)
    {
        return new static(new \ReflectionClass($name));
    }

    public function getName()
    {
        return $this->rfc->getName();
    }

    public function isAbstract()
    {
        return $this->rfc->isAbstract();
    }

    public function isFinal()
    {
        return $this->rfc->isFinal();
    }

    public function getMethods()
    {
        return array_map(function ($method) {
            return new Method($method);
        }, $this->rfc->getMethods());
    }

    public function getInterfaces()
    {
        return array_map(function ($interface) {
            return new static($interface);
        }, $this->rfc->getInterfaces());
    }

    public function __toString()
    {
        return $this->getName();
    }
}
