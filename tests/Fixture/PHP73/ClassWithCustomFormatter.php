<?php

namespace PHP73;

class ClassWithCustomFormatter
{
    public $stringProperty = 'a string';
    public $numberProperty = 123;
    private $arrayProperty = ['a', 'nother', 'array'];
    private $privateProperty = 'private';

    public function getArrayProperty()
    {
        return $this->arrayProperty;
    }
}
