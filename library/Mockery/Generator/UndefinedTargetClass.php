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

    public function getNamespaceName()
    {
        $parts = explode("\\", ltrim($this->getName(), "\\"));
        array_pop($parts);
        return implode("\\", $parts);
    }

    public function inNamespace()
    {
        return $this->getNamespaceName() !== '';
    }

    public function getShortName()
    {
        $parts = explode("\\", $this->getName());
        return array_pop($parts);
    }

    public function implementsInterface($interface)
    {
        return false;
    }

    public function hasInternalAncestor()
    {
        return false;
    }
}
