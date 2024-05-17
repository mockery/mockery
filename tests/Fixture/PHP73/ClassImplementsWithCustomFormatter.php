<?php

namespace PHP73;

class ClassImplementsWithCustomFormatter implements InterfaceWithCustomFormatter
{
    public $stringProperty = 'a string';
    public $numberProperty = 123;
    private $privateProperty = 'private';
    private $arrayProperty = ['a', 'nother', 'array'];

    public function getArrayProperty()
    {
        return $this->arrayProperty;
    }
}
