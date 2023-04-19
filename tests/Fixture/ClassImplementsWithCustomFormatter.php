<?php

namespace MockeryTest\Fixture;

use MockeryTest\Fixture\InterfaceWithCustomFormatter;

class ClassImplementsWithCustomFormatter implements InterfaceWithCustomFormatter
{
    public $stringProperty = "a string";
    public $numberProperty = 123;
    private $privateProperty = "private";
    private $arrayProperty = array('a', 'nother', 'array');
    public function getArrayProperty()
    {
        return $this->arrayProperty;
    }
}
