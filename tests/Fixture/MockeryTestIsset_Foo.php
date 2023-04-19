<?php

namespace MockeryTest\Fixture;

class MockeryTestIsset_Foo
{
    private $var;
    public function __construct($var)
    {
        $this->var = $var;
    }
    public function __get($name)
    {
        $this->var->doSomething();
    }
    public function __isset($name)
    {
        return (bool) \strlen($this->__get($name));
    }
}
