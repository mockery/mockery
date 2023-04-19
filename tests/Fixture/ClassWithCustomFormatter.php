<?php

namespace MockeryTest\Fixture;

class ClassWithCustomFormatter
{
    public $stringProperty = "a string";
    public $numberProperty = 123;
    private $arrayProperty = array('a', 'nother', 'array');
    private $privateProperty = "private";
    public function getArrayProperty()
    {
        return $this->arrayProperty;
    }
}
